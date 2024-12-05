<?php

namespace SEOPressPro\Services\Audit;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

class SEOIssuesDatabase
{

    public function saveData($postId, $data)
    {
        if(!$postId){
            return;
        }

        $items = [
            "issue_name" => isset($data['issue_name']) ? $data['issue_name'] : "",
            "issue_desc" => isset($data['issue_desc']) ? $data['issue_desc'] : "",
            "issue_type" => isset($data['issue_type']) ? $data['issue_type'] : "",
            "issue_priority" => isset($data['issue_priority']) ? $data['issue_priority'] : 0,
            "analysis_date" => isset($data['analysis_date']) ? $data['analysis_date'] : "",
            "post_id" => $postId,
        ];

        //Save only issues
        if ($items['issue_priority'] === 0 || $items['issue_priority'] === 'good') {
            return;
        }

        //Update existing issue?
        $alreadyExist = seopress_pro_get_service('SEOIssuesRepository')->issueAlreadyExistForPostId($postId, $items['issue_name']);

        if($alreadyExist){
            seopress_pro_get_service('SEOIssuesRepository')->updateSEOIssue($postId, $items);
            return;
        }

        //Insert new issue
        seopress_pro_get_service('SEOIssuesRepository')->insertSEOIssue($items);
    }

    public function getData($postId, $columns = ["*"])
    {
        $data = seopress_pro_get_service('SEOIssuesRepository')->getSEOIssue($postId, $columns);
        return $data;
    }
}
