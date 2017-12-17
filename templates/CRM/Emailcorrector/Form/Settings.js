
/**
 * Function to remove a line with values (effectively removing the setting once the save button is clicked)
 * @param element
 */
function clearLine(element) {
  var elementName = element.name;
  cj('#' + elementName).parent().parent().remove();
}
