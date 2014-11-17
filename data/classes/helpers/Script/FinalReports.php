<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_FinalReports extends Script {
    const CRON_CALLABLE = true;

    function execute() {
        $projects = Project::getManager()
            ->select()
            ->where("root.autoStopDate = (CURRENT_DATE() - INTERVAL 1 DAY)")
            ->andWhereIn('root.deleteStatus', array(Project::STATE_OK, Project::STATE_PAUSE))
            ->execute();
        $count = count($projects);
        //var_dump($count, $projects);die;
        if ($count) {
            $this->log->notice("Found $count projects");
            $creator = new RightHolderLetterCreator();
            $creator->createByCron($projects);
        }
    }
}