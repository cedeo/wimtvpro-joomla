<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

if (isset($_GET["refresh"]) && $_GET["refresh"] == "1") {
    echo '<script type="text/javascript">';
    echo '  window.parent.location.reload();
            window.parent.SqueezeBox.close();
          </script>';
}
?>



<script type="text/javascript">



    function myValidate(f) {

        if (document.formvalidator.isValid(f)) {
            f.submit();
            return true;
        }
        else {
            var msg = new Array();
            msg.push('You are not compiled all field required');
            if ($('email').hasClass('invalid')) {
                msg.push('<?php // echo JText::_('Invalid Email')   ?>');
            }
            alert(msg.join('\n'));
            return false;
        }

    }
</script>

<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_wimtvpro'); ?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate" onsubmit="return myValidate(this);">
    <fieldset>
        <div class="fltrt">
            <button type="button" onclick="Joomla.submitform('register.save', this.form);">
                <?php echo JText::_('JSAVE'); ?></button>
            <button type="button" onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : ''; ?>  window.parent.SqueezeBox.close();">
                <?php echo JText::_('JCANCEL'); ?></button>
        </div>
        <div class="configuration" >
            <?php echo JText::_("Wimtv Registration") ?>
        </div>
    </fieldset>



    <fieldset class="adminform">
        <legend>
            <?php echo JText::_('Personal Info'); ?>
        </legend>
        <ul class="adminformlist">
            <?php foreach ($this->form->getFieldset("personalInfo") as $field): ?>
                <li><?php
                    echo $field->label;
                    echo $field->input;
                    ?></li>
<?php endforeach; ?>
        </ul>
    </fieldset>

    <fieldset class="adminform">
        <legend>
<?php echo JText::_('Login Credential'); ?>
        </legend>
        <ul class="adminformlist">
                <?php foreach ($this->form->getFieldset("loginCredential") as $field): ?>
                <li><?php
                    echo $field->label;
                    echo $field->input;
                    ?></li>
<?php endforeach; ?>
        </ul>
    </fieldset>
    <fieldset class="adminform">
        <ul>
            <li>
                <a target="_new" href="http://www.wim.tv/wimtv-webapp/term.do">Terms of Service</a> 
                and
                <a target="_new" href="http://www.wim.tv/wimtv-webapp/privacy.do">Privacy Policies</a></li>
        </ul>
    </fieldset>



    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="component" value="com_wimtvpro" />
<?php echo JHtml::_('form.token'); ?>
    </div>
</form>

<?php ?>