jQuery(document).ready(function () {
    //relabel Dublin Core as Text
    jQuery("a:contains('Dublin Core'),legend:contains('Dublin Core')").text('Text');
    //add Text notes
    jQuery('span#dublin-core-description').text('USAGE NOTES: Enter the main text below, including the title, narrative text, and subject term(s). The Title should be brief and to the point, keeping in mind that only the first 40 characters will be visible on the mobile application. Narrative Text should be between 250 and 500 words in length, following an agreed-upon editorial voice established at the outset of the project. Adding an empty line between paragraphs is recommended.');
    //relabel Description as Narrative Text
    jQuery("label:contains('Description')").text('Narrative Text');
    //add File upload notes
    jQuery("h3:contains('Current Files')").before('<span id="files-description" class="element-set-description">USAGE NOTES: After saving this item, you will need to return to this page and click EDIT for each file, to add a title and description for each file. The first file uploaded will serve as the thumbnail for the item. You must upload the first file alone and return to add additional files after the upload is complete. After the first file is uploaded, multiple files may be added at the same time.</span><br>');   
    //add Tags notes
    jQuery("legend:contains('Tags')").before('<span id="tags-description" class="element-set-description">USAGE NOTES: Add tags separated by a comma (,). It is recommended to limit your total number of tags to less than 10. Tags should be chosen with the goal of connecting similar content. Thus, a useful tag will have more than two total results (as opposed to only one, which does not connect anything) and not so many that it returns irrelevant results (a tag that is applied to almost all of your content is not a useful tag).</span><br>');      
    //add Map notes
    jQuery("legend:contains('Map')").before('<span id="map-description" class="element-set-description">USAGE NOTES: To add a pin to the map, click directly on the location in the map or search for a location using the search bar.To move a pin, simply click on the desired location, or restart your search. Search bar accepts lattitude-longitude, formal addresses or names, and some informal queries such as intersection descriptions.</span><br>');  
    //remove USE HTML and other checkboxes except for Subjects
    jQuery('input.add-element, input.remove-element, label.use-html').not('#element-49 input.add-element, #element-49 input.remove-element').remove();
    //remove DC Explanations 
    jQuery('p.explanation').remove();   
    //remove File Format Metadata fields 
    jQuery("legend:contains('Format (Legacy) Metadata')").remove();   
    //add File Metadata Edit notes 
    jQuery("body.edit-file legend:contains('Text')").after('<span id="file-metadata-description" class="element-set-description">USAGE NOTES: Add a title and text for the media file. This content will be used in the media lists and captions. It is recommended to end each narrative text/caption with a formatted citation. For example, "Image Courtesy of Cleveland State University. Michael Schwartz Library. Division of Special Collections. Cleveland Press Collection."</span><br>');
    
    //==========================
	//unhide descriptions/labels
	jQuery("#section-nav.navigation.tabs li a, span.element-set-description").css('visibility','visible');     
});