<?php

namespace OCA\Invitation\Migration;

use OCA\Invitation\Db\Schema;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;
use Psr\Log\LoggerInterface;

/**
 * Creates table: invitation_constants
 */
class Version20231130125300 extends SimpleMigrationStep
{

    /** @var IDBConnection */
    private $dbc;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(IDBConnection $dbc, LoggerInterface $logger)
    {
        $this->dbc = $dbc;
        $this->logger = $logger;
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options)
    {

        /**
         * @var ISchemaWrapper $schema
         */
        $schema = $schemaClosure();

        if (!$schema->hasTable(Schema::TABLE_INVITATION_CONSTANTS)) {
            //----------------------
            // The invitations constants table
            //----------------------

            /**
             * @var \Doctrine\DBAL\Schema\Table
             */
            $table = $schema->createTable(Schema::TABLE_INVITATION_CONSTANTS);
            $table->addColumn(Schema::INVITATION_CONSTANTS_NAME, Types::STRING, [
                'length' => 255,
                'notnull' => true,
            ]);
            $table->addColumn(Schema::INVITATION_CONSTANTS_VALUE, Types::STRING, [
                'length' => 255,
                'notnull' => true,
            ]);
            $table->setPrimaryKey([Schema::INVITATION_CONSTANTS_NAME]);
        }
        return $schema;
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
	 * @return null|ISchemaWrapper
     */
    public function postSchemaChange(IOutput $output, \Closure $schemaClosure, array $options)
    {
        /**
         * @var LoggerInterface $logger
         */
        $logger = \OC::$server->getLogger();
        $logger->debug(" - table prefix: POIUGFDFGUYIHUOPJ HI BIHH UOJ ");
        
        $tableConstants = Schema::TABLE_INVITATION_CONSTANTS;
        $statement = $this->dbc->prepare(
            "INSERT INTO `oc_invitation_constants` (`name`, `value`)
		SELECT * FROM (SELECT 'invitation.received', 'received') AS tmp
		WHERE NOT EXISTS (
			SELECT name FROM oc_invitation_constants WHERE name = 'invitation.received'
		) LIMIT 1;
		INSERT INTO `oc_invitation_constants` (`name`, `value`)
		SELECT * FROM (SELECT 'invitation.sent', 'sent') AS tmp
		WHERE NOT EXISTS (
			SELECT name FROM oc_invitation_constants WHERE name = 'invitation.sent'
		) LIMIT 1;"
        );

        $statement->execute();
    }
}
