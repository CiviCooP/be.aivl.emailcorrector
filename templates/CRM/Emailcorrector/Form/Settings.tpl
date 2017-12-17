<div id="emailcorrector_upper_level" class="crm-content-block crm-block">
  {if $settings_type eq 'top'}
    {include file="CRM/Emailcorrector/Form/TopSettings.tpl"}
  {/if}
  {if $settings_type eq 'second'}
    {include file="CRM/Emailcorrector/Form/SecondSettings.tpl"}
  {/if}
  {if $settings_type eq 'fake'}
    {include file="CRM/Emailcorrector/Form/FakeSettings.tpl"}
  {/if}
  <div id="emailcorrector_footer_buttons" class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
{crmScript ext=be.aivl.emailcorrector file=templates/CRM/Emailcorrector/Form/Settings.js}

