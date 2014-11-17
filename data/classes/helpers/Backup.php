<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 20.08.12
 * Time: 15:23
 */
class Backup
{
    const BACKUP_VERSION = 1;
    const NON_OUTDATED_WEEKS = 1;
    const NON_OUTDATED_MONTHS = 1;
    const NON_OUTDATED_YEARS = 1;

    const COMPRESSED_EXTENSION = 'tgz';
    const UNCOMPRESSED_EXTENSION = 'tar';

    private $backupDirs = array(
      '/upload/files',
      '/upload/images',
      '/upload/manuals',
      '/upload/redactor',
    );

    private $infoFileName = 'info';
    private $uploadsDir = 'files';
    private $sqlDumpFileName = 'dump.sql';
    private $backupExtension = 'tar';
    private $useCompression = null;

    private $mysqldumpCommand = 'mysqldump';
    private $mysqlCommand = 'mysql';

    private static
        $backupDir,
        $tempDir,
        $backupInfos = array();

    function __construct() {

        $this->backupExtension = self::getBackupExtension();
        $this->useCompression = self::useCompression();
    }

    public static function getBackupExtension() {
        if (self::useCompression()) {
            return self::COMPRESSED_EXTENSION;
        } else {
            return self::UNCOMPRESSED_EXTENSION;
        }
    }

    public static function useCompression() {
        return function_exists('gzopen');
    }

    public function makeBackup($comment='', $additionalInfo = array()) {
        $this->blockSite();
        $this->prepareTempDir();
        $this->createDBDump();
        $this->createUploadDump();
        $this->createInfoFile($comment, $additionalInfo);
        $this->createBackupFromTemp(time());
        $this->deleteTempDir();
        $this->unblockSite();
    }

    public static function getBackupPath() {
        if (!self::$backupDir)
            self::$backupDir = str_replace('//', '/',FV_ROOT . "backups/");
        return self::$backupDir;
    }

    private function getTempPath() {
        if (!self::$tempDir)
            self::$tempDir = self::$backupDir.'temp/';
        return self::$tempDir;
    }

    private function prepareTempDir() {
        $this->deleteTempDir();
        mkdir( self::getTempPath() );
    }

    private function deleteTempDir() {
        if (is_dir(self::getTempPath())) {
            $this->recurseDelete(self::getTempPath());
        }
    }

    private function createDBDump() {
        $dumpPath = self::getTempPath().$this->sqlDumpFileName;

        $command = "echo \"SET NAMES utf8;\"  > \"$dumpPath\"";
        exec($command);

        $command = "echo \"/*!40014 SET FOREIGN_KEY_CHECKS=0 */;\"  >> \"$dumpPath\"";
        exec($command);

        $command = "{$this->mysqldumpCommand} -u ".__user." --password=\"".__password."\" --host=\"".__DB_SERVER."\" --compact --add-drop-table --disable-keys --single-transaction --default-character-set=utf8 ".__DB_NAME." >> \"$dumpPath\"";
        exec($command);

        $command = "echo \"/*!40014 SET FOREIGN_KEY_CHECKS=1 */;\"  >> \"$dumpPath\"";
        exec($command);
    }

    private function createUploadDump() {
        foreach ($this->backupDirs as $dir) {
            $this->recurseCopy(
                str_replace('//', '/', FV_WEB . $dir),
                str_replace('//', '/', self::getTempPath() . "/{$this->uploadsDir}/$dir")
            );
        }
    }

    private function recurseCopy($src, $dst) {
        $dst = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dst);
        $src = realpath(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $src));
        if (is_dir($src)) {
            @mkdir($dst, 0777, true);
            $files = scandir($src);
            foreach ($files as $file)
                if (( substr($file, 0, 1) !=  '.' )) $this->recurseCopy("$src/$file", "$dst/$file");
        }
        else if (file_exists($src)) {
            @copy($src, $dst);
        }
    }

    private function recurseDelete($dir) {
        if( strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            exec(' rm -rf ' . $dir);
            return;
        }

        $it = new RecursiveDirectoryIterator($dir);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file){
            if ($file->isDir()){
                // SOME DIRTY HACKS NEED HERE!!!
                closedir(opendir($file->getRealPath()));
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    private function createInfoFile($comment, $additionalInfo) {
        $info = array(
            'Creator' => fvSite::$fvSession->getUser() ? fvSite::$fvSession->getUser()->getPk() : '',
            'Timestamp' => time(),
            'Comment' => $comment,
            'DBVersion' => fvSite::$fvConfig->get('database.version'),
            'BackupVersion' => self::BACKUP_VERSION,
        );
        $info = array_merge($info, $additionalInfo);
        file_put_contents(self::getTempPath().$this->infoFileName, json_encode($info));
        return $info;
    }

    private function createBackupFromTemp($backupName) {
        $tarObject = new Tar( self::getBackupPath() . "$backupName.{$this->backupExtension}", $this->useCompression);
        $tarObject->createModify(self::getTempPath(), '', self::getTempPath());
    }

    public function getBackupFileList(){
        $list = (glob(self::getBackupPath() . "/*.{$this->backupExtension}"));
        foreach ($list as &$element) {
            $element = basename($element);
        }
        //array_walk($list, 'basename');
        return $list;
    }

    public function getBackupFileInfo($fileName){
        if (!array_key_exists($fileName, self::$backupInfos)) {
            $info = $this->extractFileInfo(self::getBackupPath().$fileName);
            $info['filename'] = $fileName;
            self::$backupInfos[$fileName] = $info;
        }
        return self::$backupInfos[$fileName];
    }

    private function extractFileInfo($fullpath) {
        $tarObject = new Tar($fullpath, $this->useCompression);
        $info = $tarObject->extractInString('/'.$this->infoFileName);
        $info = json_decode($info, true);
        if (empty($info)) {
            throw new Exception("Invalid backup file $fullpath");
        }
        return $info;
    }

    public function getInfoFromTempFile($fileName) {
        $tempDir = fvSite::$fvConfig->get('path.upload.temporal');
        $info = $this->extractFileInfo($tempDir.$fileName);
        return $info;
    }

    public function getAllBackupsInfo() {
        $return = array();
            foreach ($this->getBackupFileList() as $file) {
                $return[$file] = $this->getBackupFileInfo($file);
            }
        return $return;
    }

    public function restoreBackup($fileName, $makeBackup = false) {
        $info = $this->getBackupFileInfo($fileName);
        if (!$this->checkDBVersion($info)) {
            throw new Exception ('Database version do not match');
        }

        if (!$this->checkBackupVersion($info)) {
            throw new Exception ('Backup version do not match');
        }
        $this->blockSite();
        if ($makeBackup) {
            $this->makeBackup('Backup before restore');
        }
        $this->extractBackupToTemp($fileName);
        $this->restoreUploadDump();
        $this->restoreDBDump();
        $this->deleteTempDir();
        $this->unblockSite();
    }

    public function checkBackupVersion($info) {
        return ($info['BackupVersion'] == self::BACKUP_VERSION);
    }

    public function checkDBVersion($info) {
        return ($info['DBVersion'] == fvSite::$fvConfig->get('database.version'));
    }

    private function extractBackupToTemp($fileName) {
        $this->prepareTempDir();
        $tarObject = new Tar(self::getBackupPath().$fileName, $this->useCompression);
        $tarObject->extract(self::getTempPath());

    }

    private function restoreUploadDump(){
        $this->recurseCopy(
            self::getTempPath().$this->uploadsDir,
            str_replace('//', '/', FV_WEB)
        );
    }

    private function restoreDBDump() {
        $dumpPath = self::getTempPath().$this->sqlDumpFileName;
        $command = "{$this->mysqlCommand} -u ".__user." --password=\"".__password."\" --host=\"".__DB_SERVER."\" ".__DB_NAME." < \"$dumpPath\"";
        exec($command);
    }

    public static function getLockFilePath() {
        return self::getBackupPath().'.lock';
    }

    public static function blockSite() {
        $handler = fopen(self::getLockFilePath(), 'w');
        fclose($handler);
    }

    public static function unblockSite() {
        if (self::isSiteBlocked()) {
            unlink(self::getLockFilePath());
        }
    }

    public static function isSiteBlocked() {
        return file_exists(self::getLockFilePath());
    }

    function __destruct() {
        $this->unblockSite();
    }

    public function deleteBackup($fileName) {
        if ($this->getBackupFileInfo($fileName)) {
            unlink(self::getBackupPath().$fileName);
        }
    }

    public function getOutdatedBackups() {
        $infos = $this->getAllBackupsInfo();
        $dates = array();
        foreach ($infos as $info) {
            if (!empty($info['autoBackup'])) {
            $dates[$info['Timestamp']] = $info['filename'];
            }
        }
        /*
        $dates = array(
            strtotime('2012-08-23') =>1,
            strtotime('2012-08-16') =>2,
            strtotime('2012-08-09') =>3,
            strtotime('2012-08-02') =>4,
            strtotime('2012-08-30') =>5,
            strtotime('2012-09-06') =>6,
        );
*/
        ksort($dates);
        $weekGroups = array();
        $monthGroups = array();
        $yearGroups = array();
        $outdatedFiles = array();
        foreach ($dates as $date =>$fileName) {
            $weekGroups[date('YW', $date)][] = $fileName;
            $monthGroups[date('Ym', $date)][] = $fileName;
            $yearGroups[date('Y', $date)][] = $fileName;
        }
        $weekGroups = array_slice($weekGroups, 0, -(self::NON_OUTDATED_WEEKS));
        $monthGroups = array_slice($monthGroups, 0, -(self::NON_OUTDATED_MONTHS));
        $yearGroups = array_slice($yearGroups, 0, -(self::NON_OUTDATED_YEARS));
        foreach ($weekGroups as $group) {

            $group = array_slice($group, 1);
            $outdatedFiles += $group;
        }
        foreach ($monthGroups as $group) {
            $group = array_slice($group, 1);
            $outdatedFiles += $group;
        }
        foreach ($yearGroups as $group) {
            $group = array_slice($group, 1);
            $outdatedFiles += $group;
        }

        $outdatedFiles =  array_unique($outdatedFiles);
        return $outdatedFiles;
    }

}
