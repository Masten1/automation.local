<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_DailyReports extends Script {
    const CRON_CALLABLE = true;

    function execute() {
        $projects = Project::getManager()
            ->select()
            ->where("(CURRENT_DATE() - INTERVAL 1 DAY) BETWEEN DATE_SUB(root.autoStartDate,INTERVAL 1 DAY) AND DATE_SUB(root.autoStopDate,INTERVAL 1 DAY)")
            ->andWhereIn('root.deleteStatus', array(Project::STATE_OK, Project::STATE_PAUSE))
            ->execute();
        $count = count($projects);
        //var_dump($count, $projects);die;
        if ($count) {
            $this->log->notice("Found $count projects");
            $date = date('Y-m-d', strtotime('-1 day'));
            $creator = new RightHolderLetterCreator($date,$date);
            $creator->createByCron($projects);
        }
    }
}