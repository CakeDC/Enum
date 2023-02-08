<?php
use Phinx\Migration\AbstractMigration;

class CreateEnumLookups extends AbstractMigration
{
    /**
     * Creates the `enum_lookups` table.
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('enum_lookups');

        $table->addColumn('label', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => true,
        ]);

        $table->addColumn('prefix', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);

        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);

        $table->create();
    }
}
