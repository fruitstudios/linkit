$(function(){
	$('body').on('change', '.linkit-type select', function (e) {
		var linkitType = this.value;
		var linkitTypeSelect = $(this);
		var linkitContainer = linkitTypeSelect.closest('div.linkit');
		if(linkitType === '')
		{
			linkitContainer.find('.linkit-options').addClass('hidden');		
		}
		else
		{
			linkitContainer.find('.linkit-options').removeClass('hidden');		
		}
		linkitContainer.find('.linkit-type-option').addClass('hidden');
		linkitContainer.find('.linkit-'+linkitType).removeClass('hidden');
	});
});