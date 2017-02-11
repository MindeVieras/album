var PhotobumAdmin = (function() {
    "use strict";
    return {};
})();

tinyMCE.baseURL = "/assets/deps/tinymce";

PhotobumAdmin.bytesToSize = function(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes === 0) return 'n/a';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    if (i === 0) return bytes + ' ' + sizes[i];
    return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
};

PhotobumAdmin.getRemotefilesize = function(url){
	size = 0;
  val = {
      file_url_remote: url
  };
  var remoteSize = $.ajax({
      type: "POST",
      data: val,
		  async: false,
      url: '/api/utilities/get-file-size-remote',
      dataType: "json",
      success: function (data) {
		    return data.msg;
		  }, 
  });
  size = remoteSize.responseJSON;
  return size;

	
};