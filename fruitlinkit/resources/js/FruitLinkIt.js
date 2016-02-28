(function($){

	FruitLinkIt = Garnish.Base.extend({

		$field: null,
		$typeSelect: null,
		$optionsHolder: null,
		$settingsHolder: null,
		$options: null,

		type: null,

		init: function(id)
		{
			this.$field = $('#'+id);

			this.$typeSelect = this.$field.find('.fruitlinkit-type select');
			this.type = this.$typeSelect.val();

			this.$optionsHolder = this.$field.find('.fruitlinkit-options');
			this.$settingsHolder = this.$field.find('.fruitlinkit-settings');
			this.$options = this.$optionsHolder.find('.fruitlinkit-option');

			this.addListener(this.$typeSelect, 'change', 'onChangeType');
		},
		
		onChangeType: function(e)
		{
			var $select = $(e.currentTarget);
			this.type = $select.val();

			if(this.type === '')
			{
				this.$optionsHolder.add(this.$settingsHolder).addClass('hidden');
			}
			else
			{
				this.$optionsHolder.add(this.$settingsHolder).removeClass('hidden');
			}

			this.$options.addClass('hidden');
			this.$options.filter('.fruitlinkit-' + this.type).removeClass('hidden');
		}

	});

})(jQuery);
