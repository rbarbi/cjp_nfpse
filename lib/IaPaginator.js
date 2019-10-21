function IaPaginator(config){
	var myPaginator = new YAHOO.widget.Paginator(config);
	myPaginator.setAttributeConfig('previousPageLinkLabel', {value : "< anterior" });
	myPaginator.setAttributeConfig('nextPageLinkLabel', {value : "proximo >" });
	myPaginator.setAttributeConfig('pageReportTemplate', {value : '({currentPage} de {totalPages})' });
	return myPaginator;
}
