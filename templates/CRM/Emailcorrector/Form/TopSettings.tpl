<div id="help">
  {ts}Het gaat hier om de correctie van het deel van het e-mailadres achter de punt. Dus bijvoorbeeld .con wordt gecorrigeerd naar .com.
    Als je een regel invult moet je zowel het deel met het foute patroon als de correctie invullen. Lege regels worden niet opgeslagen, die kun je rustig laten staan.{/ts}
</div>

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>
<div id="email_corrector_form_top_wrapper" class="dataTables_wrapper">
  <table id="email_corrector_form_top_-table" class="display">
    <thead>
      <tr>
        <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Te corrigeren waarde{/ts}</th>
        <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Vervangen door{/ts}</th>
        <th class="sorting_disabled" rowspan="1" colspan="1"></th>
      </tr>
    </thead>
    <tbody>
      {foreach from=$elementNames key=source_field item=target_field}
      <tr id="row_{$source_field}" class="{cycle values="odd-row,even-row"}">
        <td>{$form.$source_field.html}</td>
        <td>{$form.$target_field.html}</td>
        <td><a class="action-link" href="#" onclick="clearLine({$source_field})" name="clearLine">{ts}Verwijderen{/ts}</a></td>
      </tr>
    {/foreach}
    </tbody>

  </table>
</div>


