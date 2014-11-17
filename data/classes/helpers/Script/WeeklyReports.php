<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_WeeklyReports extends Script {
    const CRON_CALLABLE = true;

    function execute() {
        $projects = Project::getManager()
            ->select()
            ->where("(WEEK(CURRENT_DATE(), 1)-1) BETWEEN WEEK(root.autoStartDate, 1) AND WEEK(root.autoStopDate, 1)")
            ->andWhereIn('root.deleteStatus', array(Project::STATE_OK, Project::STATE_PAUSE))
            ->execute();
        $count = count($projects);
        if ($count) {
            $this->log->notice("Found $count projects");
            $now = strtotime('-1 week');
            $startDate = (date("w", $now)==1) ? $now : strtotime('last monday', $now);
            $endDate = (date("w", $now)==0) ? $now : strtotime('next sunday', $now);
            $creator = new RightHolderLetterCreator(date('Y-m-d', $startDate), date('Y-m-d', $endDate));
            $creator->createByCron($projects);
        }
    }
}