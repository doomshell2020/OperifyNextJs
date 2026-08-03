<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\Network\Email\Email;
use Cake\Datasource\ConnectionManager;
use ZipArchive;

include '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
include '../vendor/phpmailer/phpmailer/src/Exception.php';
include '../vendor/phpmailer/phpmailer/src/SMTP.php';
include '../vendor/phpmailer/phpmailer/src/OAuth.php';
include '../vendor/autoload.php';

class CronController extends AppController
{
  public function initialize()
  {
    parent::initialize();
  }

  public function beforeFilter(Event $event)
  {
    $this->loadModel('Users');
    parent::beforeFilter($event);
    $this->loadComponent('Cookie');
    $this->Auth->allow(['dataBaseBackup', 'sendDatabaseBackup']);
  }


  public function sendDatabaseBackup()
  {

    $host = DBHOSTNAME;
    $user = MYSQLUSERNAME;
    $pass = MYSQLPASSWORD;
    $backupDirectory = ROOT . '/webroot/DbBackup/';
    $mysqli = new \mysqli($host, $user, $pass);
    if ($mysqli->connect_error) {
      die('Connection failed: ' . $mysqli->connect_error);
    }

    $databases = $mysqli->query("SHOW DATABASES");
    if (!$databases) {
      die('Error: ' . $mysqli->error);
    }

    $message = "Please find the attached database backup.";
    $subject = 'Operify ERP DB backup downloaded successfully: ' . date("d-m-Y");
    $to = 'rahulbishnoi0789@gmail.com';
    Email::configTransport('gmail', [
      'host' => 'smtp.gmail.com',
      'port' => 587,
      'username' => 'sanjay@doomshell.com',
      'password' => 'Sanjay@1223',
      'className' => 'Smtp',
      'tls' => true
    ]);

    $email = new Email();
    $email->transport('gmail');
    $res = $email->from(['sanjay@doomshell.com' => 'Operify.in'])
      ->to($to)
      ->subject($subject)
      ->emailFormat('html');

    $zipFiles = [];
    $attachmentSqlFiles = [];

    while ($row = $databases->fetch_assoc()) {
      $dbname = $row['Database'];

      if ($dbname == 'information_schema' || $dbname == 'mysql' || $dbname == 'performance_schema') {
        continue;
      }
      if ($dbname == "operify" || $dbname == "tirupati_ebm" || $dbname == "tirupati_tppl" || $dbname == "tirupati_kcpl" || $dbname == "tirupati_kcpl2") {
        $backupFileName = $backupDirectory . $dbname . '_backup_' . date("d_m_Y") . '.sql';
        $zipFileName = $backupDirectory . $dbname . '_backup_' . date("d_m_Y") . '.zip';


        $zipFiles[] = [
          'file' => $zipFileName,
          'mimetype' => 'application/zip',
          'contentId' => 'backup' . $zipFileName,
        ];

        $attachmentSqlFiles[] = [
          'file' => $backupFileName,
          'mimetype' => 'application/sql',
          'contentId' => 'backup' . $backupFileName,
        ];
      }
    }
    $allAttachments = array_merge($zipFiles, $attachmentSqlFiles);
    $email->attachments($allAttachments);
    // $email->addCC('rajesh@doomshell.com');

    $res = $email->send($message);
    if ($res) {
      foreach ($zipFiles as $key => $file) {
        unlink($file['file']);
        unlink($attachmentSqlFiles[$key]['file']);
      }
      echo "sent";
      die;
    }

    echo "Something Wrong";
    die;
  }

  public function dataBaseBackup()
  {
    $host = DBHOSTNAME;
    $user = MYSQLUSERNAME;
    $pass = MYSQLPASSWORD;
    $dbname = DB_NAME;
    $backupDirectory = ROOT . '/webroot/DbBackup/';

    $mysqli = new \mysqli($host, $user, $pass);
    if ($mysqli->connect_error) {
      die('Connection failed: ' . $mysqli->connect_error);
    }

    $databases = $mysqli->query("SHOW DATABASES");
    if (!$databases) {
      die('Error: ' . $mysqli->error);
    }

    while ($row = $databases->fetch_assoc()) {
      $dbname = $row['Database'];

      // Skip system databases if needed
      if ($dbname === 'information_schema' || $dbname === 'mysql' || $dbname === 'performance_schema') {
        continue;
      }
      if ($dbname == "operify" || $dbname == "tirupati_ebm" || $dbname == "tirupati_tppl" || $dbname == "tirupati_kcpl" || $dbname == "tirupati_kcpl2") {


        $mysqli->select_db($dbname);

        $tables = $mysqli->query("SHOW TABLES");
        if (!$tables) {
          die('Error: ' . $mysqli->error);
        }

        $backupContent = '';

        while ($tableRow = $tables->fetch_row()) {
          $table = $tableRow[0];

          $structure = $mysqli->query("SHOW CREATE TABLE $table");
          $createTableSQL = $structure->fetch_row()[1];

          $data = $mysqli->query("SELECT * FROM $table");
          $insertDataSQL = '';

          while ($rowData = $data->fetch_assoc()) {
            $values = array_map([$mysqli, 'real_escape_string'], array_values($rowData));
            $insertDataSQL .= "INSERT INTO $table VALUES ('" . implode("', '", $values) . "');\n";
          }

          $backupContent .= "DROP TABLE IF EXISTS $table;\n" . $createTableSQL . ";\n\n" . $insertDataSQL . "\n\n\n";
        }

        $mysqli->next_result(); // Move to the next result set

        $backupFileName = $backupDirectory . $dbname . '_backup_' . date("d_m_Y") . '.sql';
        file_put_contents($backupFileName, $backupContent);
        @chmod($backupFileName, 0777);

        $zipFileName = $backupDirectory . $dbname . '_backup_' . date("d_m_Y") . '.zip';

        $zip = new \ZipArchive();
        // pr($zipFileName);exit;

        if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
          $zip->addFile($backupFileName, basename($backupFileName));
          $zip->close();
          @chmod($zipFileName, 0777);
          echo 'File compressed successfully for database ' . $dbname . '.';
        } else {
          echo 'Failed to create zip archive for database ' . $dbname . '.';
        }
        echo 'Backup completed successfully for database ' . $dbname . '!';
      }
    }
    $mysqli->close();
    $this->sendDatabaseBackup();
    echo 'Mail Send';
    die;
  }
}
