<?php

return [
    /*
     * The database connection to be used.
     */
    'connection'         => null,

    /*
     * Where to store new generated migration files.
     */
    'output_path'        => database_path('migrations'),

    /*
     * How to name new created files.
     * Available placeholders:
     * - {date}   The current date & time in Y_m_d_His format
     * - {table}  The table name
     */
    'file_name_template' => '{date}_create_{table}_table.php',

    /*
     * Weather to append the existing charset to columns.
     */
    'append_charset'     => true,

    /*
     * Weather to append the existing collation to columns.
     */
    'append_collation'   => true,
];
