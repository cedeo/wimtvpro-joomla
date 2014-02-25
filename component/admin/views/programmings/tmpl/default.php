<!--?php echo url('admin/config/wimtvpro/programmings/edit', array("query" => array("namefunction" => "new"))); ?-->
<form action="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=programmings'); ?>" method="post" name="adminForm">

    <table class="adminlist wimtvpro">
        <thead>
        <tr>
            <th>Title</th>
            <th>Edit</th>
            <th>Remove</th>
            <!--th>Shortcode</th-->
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->programmings as $prog){
            if (!isset($prog->name) )
                $titleProgramming = "No title";
            else
                $titleProgramming = $prog->name;
            ?>
            <tr>
                <td><?php echo $titleProgramming; ?></td>
                <td>
                    <?php
                     //echo url('admin/config/wimtvpro/programmings/edit', array("query" => array("progId" => $prog->identifier, "title" => $titleProgramming)));
                     //echo url('admin/config/wimtvpro/programmings', array("query" => array("functionList" => "delete", "id" => $prog->identifier)));
                    ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=programmings&layout=edit&progId=' . $prog->identifier . '&title=' . $titleProgramming) ?>" >
                        Edit
                    </a>
                </td>
                <td>
                    <a href="<?php echo JRoute::_('index.php?option=com_wimtvpro&view=programmings&task=delete&progId=' . $prog->identifier) ?>" >
                        Remove
                    </a>
                </td>
                <!--td>
                    <textarea style="resize: none; width:90%;height:100%;" readonly='readonly'
                              onclick="this.focus(); this.select();">[wimprog id="<?php echo $prog->identifier;?>"]</textarea>
                </td-->
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value="" />
</form>