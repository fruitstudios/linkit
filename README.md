# Linkit plugin for Craft

One link field to replace them all, a multi-purpose link fieldtype for Craft CMS.

This plugin adds a fieldtype which links to all sorts of stuff, Linkit can currently link to:

* Entries
* Assets
* Categories
* Emails
* Phone numbers
* Custom URLs

And it plays really nicely with Matrix!

Field settings allow you to:

* Configure what elements each field can link too.
* Set which entry/asset sources are available to each field.
* Allow fields to set custom link text.
* Allow fields to set links to open in new window.
* Set default link text.


## Installation

To install Linkit, follow these steps:

1.  Upload the linkit/ folder to your craft/plugins/ folder.
2.  Go to Settings > Plugins from your Craft control panel and enable the Linkit plugin.
3.  Create and configure a new linkit field.

## Template Usage

Get the link object:

	{% set yourLink = entry.yourLinkFieldHandle %}
	
Just the link - output a pre built HTML link:

	{{ yourLink.link|raw }}
			
Build a simple custom link:

	<a href="{{ yourLink.url }}" {{ yourLink.target ? ' target="_blank"' }} title="{{ yourLink.linkText }}">{{ yourLink.linkText }}</a>
			
Build your own:			
 
	{% switch yourLink.type %}
	
	    {% case "entry" %}
	    
			<a href="{{ yourLink.entry.url }}" {{ yourLink.target ? ' target="_blank"' }} title="{{ yourLink.entry.title }}">{{ yourLink.entry.title }}</a>
			
	    {% case "category" %}
	    
			<a href="{{ yourLink.category.url }}" {{ yourLink.target ? ' target="_blank"' }} title="{{ yourLink.category.title }}">{{ yourLink.category.title }}</a>

	    {% case "asset" %}
						    
			<a href="{{ yourLink.asset.url }}" {{ yourLink.target ? ' target="_blank"' }} title="{{ yourLink.asset.title }}">
				{% if yourLink.asset.kind == 'image' %}
					<img src="{{ yourLink.asset.url('thumb') }}" />
				{% else %}
					<img src="thumb-{{ yourLink.asset.kind }}.png" />
				{% endif %}
			</a>		
	
	    {% case "custom" %}
	    
			<a href="{{ yourLink.custom }}" {{ yourLink.target ? ' target="_blank"' }} title="{{ yourLink.linkText }}">
				<i class="custom-link-icon"></i> {{ yourLink.linkText }}
			</a>
			
	    {% case "email" %}
	    
			<a href="mailto:{{ yourLink.email }}" {{ yourLink.target ? ' target="_blank"' }} title="{{ yourLink.linkText }}">{{ yourLink.email }}</a></p>
	
	    {% case "tel" %}
	    
			<a href="tel:{{ yourLink.tel }}" {{ yourLink.target ? ' target="_blank"' }} title="{{ yourLink.linkText }}">{{ yourLink.tel }}</a></p>
	
	{% endswitch %}
	
## Template Variables

Each link returns a link object which contains the following:

	{% set link = entry.yourLinkFieldHandle %} // Get the link

	{{ yourLink.type }} // Returns the link type - entry, asset, email, tel, custom

	{{ yourLink.email }}  	// Email String / False
	{{ yourLink.custom }} 	// Custom URL String / False
	{{ yourLink.tel }}		// Telephone Number / False
	{{ yourLink.entry }}	// Entry Object / False
	{{ yourLink.category }}	// Category Object / False
	{{ yourLink.asset }}	// Asset Object / False
	// Each link type is returned - only the active type will return data the rest return false

	{{ yourLink.text }} 	// The Custom Text String 
	{{ yourLink.target }}   // True/False (Bool) - Open in new window? 
	
	{{ yourLink.url }}		// The full url (correct prefix added eg mailto: or tel:)
	{{ yourLink.linkText }} // The link text string ready to use (If no custom text is provided it generates it based on the link type)
	
	{{ yourLink.link }}		// Full link HTML ready to use	

## Roadmap

* Force download option
* Rework the way link data is returned
* Improve handling of target stuff - if it's required
* More Validation Options


## Changelog

### 0.9.1

* Fixed: Input field not displaying correctly when set to single type when field had previously been saved. 
* Fixed: Custom text returning false.

### 0.9

* Added: Removed the requirement to use the |raw filter when using the link variable.
* Fixed: Input field now corectly displays when on one link type is setup.

### 0.8.1

* Added: Hide the Link To... select when only one link type has been selected for the field.

### 0.8

* Added: Category Support

### 0.7

* Added: Default Text - Fieldtype setting to add default link text for a link.

### 0.6

* Fix: PHP Error when returning an entry or asset that has subsequently been deleted

### 0.5

* Initial beta release

## Licence

Copyright 2014 Fruit Studios Ltd
