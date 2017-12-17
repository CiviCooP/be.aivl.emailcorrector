<div id="help">
  {ts}LET OP: De e-mailadressen die je hier opgeeft worden verwijderd uit de database!{/ts}
</div>

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>
{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}&nbsp;<a class="action-link" href="#" onclick="clearLine({$form.$elementName.name})" name="clearLine">{ts}Verwijderen{/ts}</a></div>
    <div class="clear"></div>
  </div>
{/foreach}
