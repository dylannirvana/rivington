<?php

namespace SEOPressPro\Services\Table;

defined( 'ABSPATH' ) || exit;

use SEOPressPro\Models\Table\TableInterface;
use SEOPressPro\Core\Table\TableFactory;
use SEOPressPro\Models\Table\TableStructure;
use SEOPressPro\Models\Table\TableColumn;
use SEOPressPro\Models\Table\Table;


class TableList {

    public function getTableSignificantKeywords(){
        $tableStructureImportantKeywords = new TableStructure([
            new TableColumn('id', [
                'type' => 'bigint(20)',
                'primaryKey' => true
            ]),
            new TableColumn('post_id', [
                'type' => 'bigint(20)',
                'index' => true,
            ]),
            new TableColumn('word', [
                'type' => 'varchar(100)',
                'index' => true,
            ]),
            new TableColumn('count', [
                'type' => 'int(11)',
            ]),
            new TableColumn('tf', [
                'type' => 'float',
            ]),
        ]);

        return new Table("seopress_significant_keywords", $tableStructureImportantKeywords, 1);
    }

    public function getTableSEOIssues(){
        $tableStructure = new TableStructure([
            new TableColumn('id', [
                'type' => 'bigint(20)',
                'primaryKey' => true
            ]),
            new TableColumn('post_id', [
                'type' => 'bigint(20)',
                'index' => true,
            ]),
            new TableColumn('issue_name', [
                'type' => 'longtext',
            ]),
            new TableColumn('issue_desc', [
                'type' => 'longtext',
            ]),
            new TableColumn('issue_type', [
                'type' => 'text',
            ]),
            new TableColumn('issue_priority', [
                'type' => 'text',
            ]),
        ]);

        return new Table("seopress_seo_issues", $tableStructure, 1);
    }

    public function getTables(){
        return [
            "seopress_significant_keywords" => $this->getTableSignificantKeywords(),
            "seopress_seo_issues" => $this->getTableSEOIssues(),
        ];
    }
}
