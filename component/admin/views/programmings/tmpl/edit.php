<a href='<?php echo JRoute::_('index.php?option=com_wimtvpro&view=programmings'); ?>' class='add-new-h2'><?php echo 'Return to list' ?></a>
<br />
<style type="text/css">@import url("http://www.wim.tv/wimtv-webapp/css/fullcalendar.css")</style>
<style type="text/css">@import url("http://www.wim.tv/wimtv-webapp/css/programming.css")</style>
<style type="text/css">@import url("http://www.wim.tv/wimtv-webapp/css/jquery-ui/jquery-ui.custom.min.css")</style>
<style type="text/css">@import url("http://www.wim.tv/wimtv-webapp/css/jquery.fancybox.css")</style>
<div id="progform">
    <form>
        <label><?php echo "Give a name to this programming (not mandatory)"; ?></label>
        <input type="text" value="<?php echo $this->nameProgramming;?>" id="progname" class="form-text"/>
        <input type="submit" value="Send" class="button submitnow form-submit" />
        <input type="submit" value="Skip" class="button submitnow form-submit" />
    </form>
</div>
<!-- calendar -->
<div id="calendar"></div>

<div style="display:none">
    <div class="embedded">
        <textarea id="progCode" onclick="this.focus(); this.select();"></textarea>
    </div>
</div>
<?php
$baseRoot = "http://www.wim.tv/wimtv-webapp/";
?>
<script type="text/javascript">
    var url_pathPlugin = "<?php echo JURI::base() . 'components/com_wimtvpro' ?>" + "/";
</script>
<script type="text/javascript">
    var programmingBase = "<?php echo JURI::base() . 'components/com_wimtvpro/includes/programmings.php' ?>";
</script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $baseRoot . 'script/jquery-ui.custom.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo $baseRoot . 'script/jquery.fancybox.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo $baseRoot . 'script/jquery.mousewheel.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo $baseRoot . 'script/fullcalendar/fullcalendar.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo $baseRoot . 'script/utils.js' ?>"></script>
<script type="text/javascript" src="<?php echo $baseRoot . 'script/programming/programming.js' ?>"></script>
<script type="text/javascript" src="<?php echo $baseRoot . 'script/programming/calendar.js' ?>"></script>
<script type="text/javascript" src="<?php echo $baseRoot . 'script/jquery-ui.custom.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo JURI::base() . 'components/com_wimtvpro/assets/js/programming-api.js' ?>"></script>