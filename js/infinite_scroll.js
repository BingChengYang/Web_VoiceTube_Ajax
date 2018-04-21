var maxPageNum = 3;

function getRequestString()
{
	if(this.pageIndex < maxPageNum)
	{
		// Get current page number from infinite scroll api
		var nextPage = this.pageIndex+1;
		return 'ajax_response_thumb.php?page=' + nextPage;
	}
}

$('#thumb').infiniteScroll({
  path: getRequestString,
  append: '.single-thumb',
  status: '.page-load-status'
});