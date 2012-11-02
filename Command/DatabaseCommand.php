<?php

namespace Kachkaev\DropboxBackupBundle\Command;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

use Kachkaev\DropboxBackupBundle\DependencyInjection\Configuration;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Kachkaev\DropboxBackupBundle\DropboxUploader;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class DatabaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dropbox_backup:database')
            ->setDescription('Backups database to dropbox')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    	// 
    	// See http://ericsilva.org/2012/07/05/backup-mysql-database-to-dropbox/
    	//

    	// Location of temp directory
    	$tmpDir = $this->getContainer()->getParameter('kernel.cache_dir')."/db/";
    	if (!file_exists($tmpDir))
    		mkdir($tmpDir);
    	
    	// Database props
    	$dbDriver = $this->getContainer()->getParameter("kachkaev_dropbox_backup.database_driver");
    	$dbHost = $this->getContainer()->getParameter("kachkaev_dropbox_backup.database_host");
    	$dbPort = $this->getContainer()->getParameter("kachkaev_dropbox_backup.database_port");
    	$dbName = $this->getContainer()->getParameter("kachkaev_dropbox_backup.database_dbname");
    	$dbUser = $this->getContainer()->getParameter("kachkaev_dropbox_backup.database_user");
    	$dbPassword = $this->getContainer()->getParameter("kachkaev_dropbox_backup.database_password");

    	$outputPrefix = $this->getContainer()->getParameter("kachkaev_dropbox_backup.database_output_fileprefix");
    	$outputDest = $this->getContainer()->getParameter("kachkaev_dropbox_backup.database_output_directory");
    	
    	// Dropbox props
    	$dropboxUser = $this->getContainer()->getParameter("kachkaev_dropbox_backup.dropbox_user");
    	$dropboxPassword = $this->getContainer()->getParameter("kachkaev_dropbox_backup.dropbox_password");
    	 
    	$output->writeln('Dumping database <info>'.$dbName.'</info> to <info>'.$dropboxUser.'</info>\'s Dropbox');
    	
    	// Get time of a dump
    	$timezone = date_default_timezone_get();
    	date_default_timezone_set('UTC');
    	$date = date('Y_m_d_H_i_s');
    	date_default_timezone_set($timezone);

    	// Create the database backup file
    	$sqlFilename = $outputPrefix.$date.".sql";
    	$sqlFile = $tmpDir.$sqlFilename;
    	if ($dbDriver == 'pdo_mysql') {
    	    $createBackup = "mysqldump --user=$dbUser --password=$dbPassword --host=$dbHost --port=$dbPort $dbName > $sqlFile";
    	} else {
    	    throw new InvalidConfigurationException("Sorry, $dbDriver database driver is not yet supported. Only use of pdo_mysql is allowed.");
    	}
    	exec($createBackup, $out);
    	
    	// Compress the file if needed
    	if ($this->getContainer()->getParameter("kachkaev_dropbox_backup.database_output_compression")) {
        	$backupFilename = $outputPrefix.$date.".tgz";
        	$backupFile = $tmpDir.$backupFilename;
        	
        	$createTar = "tar cvzf $backupFile -C $tmpDir $sqlFilename 2>/dev/null";
        	exec($createTar, $out);
    	} else {
    	    $backupFilename = $sqlFilename;
    	    $backupFile = $sqlFile;
    	}
    	
    	// Upload database backup to Dropbox
    	$uploader = new DropboxUploader($dropboxUser, $dropboxPassword);
    	$uploader->upload($backupFile, $outputDest,  $backupFilename);
    	
    	// Delete temporary files
    	@unlink($sqlFile);
    	@unlink($backupFile);
    	
    	$output->writeln('Done.');
    }
}