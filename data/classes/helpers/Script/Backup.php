<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_Backup extends Script {
    const CRON_CALLABLE = true;

    function execute() {
        $backup = new Backup();
        $backup->makeBackup('Automatic backup', array('autoBackup'=>true));
        $backupsForDeletion = $backup->getOutdatedBackups();
        foreach ($backupsForDeletion as $fileName) {
            $this->log->notice("Deleting {$fileName}");
            $backup->deleteBackup($fileName);
        }
    }
}