<?php

namespace Photobum\Utilities;

use Photobum\Config;


class Twig extends \Twig_Environment
{

    public function __construct()
    {
        $this->f3 = \Base::instance();
        if (PHP_SAPI === 'cli') {
            return false;
        }
        $this->onReadyArray = [];
        $this->onLoadArray = [];
        $this->inject = [];

        $debug = (Config::get('ENVIRONMENT') == 'development') ? true : false;

        $laSettings['debug'] = $debug;
        $laSettings['charset'] = 'utf-8';
        $laSettings['base_template_class'] = 'Twig_Template';
        $laSettings['cache'] = '/tmp/twig/';
        $laSettings['auto_reload'] = true;
        $laSettings['strict_variables'] = false;
        $laSettings['optimizations'] = -1;

        $loader = new \Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/app/templates');

        parent::__construct($loader, $laSettings);
        $this->addExtension(new \Twig_Extensions_Extension_Text());
        $this->addGlobal('on_ready', null);

        // Image function
        $func = new \Twig_SimpleFunction(
            'img',
            function ($url, $width, $height) {

                //$path = dirname($url);
                $name = basename($url);
                $style_path = DS.'cache'.DS.'styles'.DS.$width.'x'.$height;

                $cache_file = $style_path.DS.$name;

                if(!file_exists(getcwd().$cache_file)){
                    return '/default.png';
                } else {
                    return $cache_file;
                }
            }
        );
        $this->addFunction($func);

        // Video function
        $func = new \Twig_SimpleFunction(
            'video',
            function ($url, $size) {

                //$path = dirname($url);
                $name = basename($url);
                $style_path = DS.'media'.DS.'videos'.DS.$size;

                $sized_file = $style_path.DS.$name;

                if(!file_exists(getcwd().$sized_file)){
                    return $url;
                } else {
                    return $sized_file;
                }
            }
        );
        $this->addFunction($func);
        
        $func = new \Twig_SimpleFunction(
            'get_ini',
            function ($name) {
                $ini = ini_get($name);
                return $ini;
            }
        );
        $this->addFunction($func);
        
        // $func = new \Twig_SimpleFunction(
        //     'ini_set',
        //     function ($name, $val) {
        //         ini_set($name, $val);
        //     }
        // );
        // $this->addFunction($func);

        $func = new \Twig_SimpleFunction(
            'd',
            function ($var) {
                if (Config::get('ENVIRONMENT') == 'development') {
                    d($var);
                }
            }
        );
        $this->addFunction($func);

        $func = new \Twig_SimpleFunction(
            'ddd',
            function ($var) {
                if (Config::get('ENVIRONMENT') == 'development') {
                    ddd($var);
                }
            }
        );
        $this->addFunction($func);

        $func = new \Twig_SimpleFunction(
            's',
            function ($var) {
                if (Config::get('ENVIRONMENT') == 'development') {
                    s($var);
                }
            }
        );
        $this->addFunction($func);

        $filter = new \Twig_SimpleFilter('htmltruncate', function ($string, $count) {
            return $this->htmlTruncateFilter($string, $count);
        });

      //  $filter = new \Twig_SimpleFilter('htmltruncate', [$this , 'htmlTruncate']);
        $this->addFilter($filter);


    }


    public function getPath() {
      $paths = ($this->loader->getPaths());
      return(array_shift($paths));
    }

    public function onReady($func)
    {
        $this->onReadyArray[] = $func;
        $this->addGlobal('on_ready', $this->onReadyArray);
    }

    public function onLoad($func)
    {
        $this->onLoadArray[] = $func;
        $this->addGlobal('on_load', $this->onLoadArray);
    }

    private function htmlTruncateFilter($html, $maxLength)
    {
        mb_internal_encoding("UTF-8");
        $printedLength = 0;
        $position = 0;
        $tags = array();
        $out = "";

        while ($printedLength < $maxLength && $this->mb_preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position))
        {
            list($tag, $tagPosition) = $match[0];

            // Print text leading up to the tag.
            $str = mb_substr($html, $position, $tagPosition - $position);
            if ($printedLength + mb_strlen($str) > $maxLength)
            {
                $out .= mb_substr($str, 0, $maxLength - $printedLength);
                $printedLength = $maxLength;
                break;
            }

            $out .= $str;
            $printedLength += mb_strlen($str);

            if ($tag[0] == '&')
            {
                // Handle the entity.
                $out .= $tag;
                $printedLength++;
            }
            else
            {
                // Handle the tag.
                $tagName = $match[1][0];
                if ($tag[1] == '/')
                {
                    // This is a closing tag.

                    $openingTag = array_pop($tags);
                    assert($openingTag == $tagName); // check that tags are properly nested.

                    $out .= $tag;
                }
                else if ($tag[mb_strlen($tag) - 2] == '/')
                {
                    // Self-closing tag.
                    $out .= $tag;
                }
                else
                {
                    // Opening tag.
                    $out .= $tag;
                    $tags[] = $tagName;
                }
            }

            // Continue after the tag.
            $position = $tagPosition + mb_strlen($tag);
        }

        // Print any remaining text.
        if ($printedLength < $maxLength && $position < mb_strlen($html))
            $out .= mb_substr($html, $position, $maxLength - $printedLength);

        // Close any open tags.

        while (substr($out, -1) != ' ') {
            $out = substr($out, 0, -1);
        }
        $out = sprintf('%s...', substr($out, 0, -1));
        while (!empty($tags))
            $out .= sprintf('</%s>', array_pop($tags));

        return $out;
    }

    private function mb_preg_match(
        $ps_pattern,
        $ps_subject,
        &$pa_matches,
        $pn_flags = 0,
        $pn_offset = 0,
        $ps_encoding = NULL
    ) {
        // WARNING! - All this function does is to correct offsets, nothing else:
        //(code is independent of PREG_PATTER_ORDER / PREG_SET_ORDER)

        if (is_null($ps_encoding)) $ps_encoding = mb_internal_encoding();

        $pn_offset = strlen(mb_substr($ps_subject, 0, $pn_offset, $ps_encoding));
        $ret = preg_match($ps_pattern, $ps_subject, $pa_matches, $pn_flags, $pn_offset);

        if ($ret && ($pn_flags & PREG_OFFSET_CAPTURE))
            foreach($pa_matches as &$ha_match) {
                $ha_match[1] = mb_strlen(substr($ps_subject, 0, $ha_match[1]), $ps_encoding);
            }

        return $ret;
    }
}