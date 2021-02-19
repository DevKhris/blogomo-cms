<?php

namespace App\Command;

use DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class InitDB extends Command
{
    private $settings;

    public function __construct(Container $container)
    {
        parent::__construct();
        $this->settings = $container->get('settings');
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('db:migrate')

            // the short description shown while running "php bin/console list"
            ->setDescription('Initialize database')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Create database structure and add initial data');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $this->settings['doctrine']['connection']['host'];
        $dbname = $this->settings['doctrine']['connection']['dbname'];
        $data = [
            'user' => $this->settings['doctrine']['connection']['user'],
            'pass' => $this->settings['doctrine']['connection']['password']
        ];
        $link = new \PDO("mysql:host=$host;dbname=$dbname", $data['user'], $data['pass']);
        
        if (!$link) {
            $output->writeLn('Can\'t open database [' . $this->settings['doctrine']['connection']['dbname'] . ']');
            return;
        }
        $queries = [
            "CREATE TABLE posts (id int primary key not null, image char(100) default null , title char(100) default null, slug char(200) not null, content text not null);",
            "INSERT INTO posts VALUES(1,'','First blog post','first-blog-post','This is sample blog post. If you see this content, doctrine is working fine.');",
            "CREATE TABLE users (id int primary key not null, username char(30) not null, password char(60) not null, first_name char(50), last_name char(50), email char(50));",
            "INSERT INTO users VALUES(1,'admin','\$2y\$10\$h2DgpuQvOWhpVmthACoKTuEVQHwHvcg5WjUekdvZx41hukm6LaUzy', 'Jhon', 'Doe', 'admin@admin.com');",
        ];

        foreach ($queries as $query) {
            $output->writeLn("[+] Migration Start");
            $stmt = $link->prepare($query);
            if (!$stmt->execute()) {
                $output->writeLn('[-] Can\'t execute statement "' . $query . '" : ');
                $link->rollBack();
                return false;
            } 
            $output->writeLn("[+] Migration Done");
        }

        $output->writeLn("Database structure created");
    }
}