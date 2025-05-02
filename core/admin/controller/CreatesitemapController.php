<?php

namespace core\admin\controller;

use core\base\controller\BaseMethods;

class CreatesitemapController extends BaseAdmin {

    use BaseMethods;

    protected $all_links = [];
    protected $temp_links = [];
    protected $bad_links = [];

    protected $maxLinks = 5000;

    protected $parsingLogFile = 'parsing_log.txt';
    protected $fileArr = ['jpg', 'png', 'jpeg', 'gif', 'xls', 'xlsx', 'pdf', 'mp4', 'mpeg', 'mp3'];

    protected $filterArr = [
        'url' => [],
        'get' => []
    ];

    public function inputData($links_counter = 1, $redirect = true) {
        $links_counter = $this->clearNum($links_counter);

        if (!function_exists('curl_init')) {

            $this->cancel(0, 'CURL library is absent. Sitemap creation is impossible', '', true);

        }

        if (!$this->userId) $this->execBase();

        if (!$this->checkParsingTable()) {

            $this->cancel(0, "You have a problem with the database table 'parsing_data'", '', true);
        };

        set_time_limit(0);

        $reserve = $this->model->get('parsing_data')[0];

        $table_rows = [];

        foreach ($reserve as $name => $item) {

            $table_rows[$name] = '';

            if ($item) $this->$name = json_decode($item);
            elseif ($name === 'all_links' || $name === 'temp_links') $this->$name = [SITE_URL];
        }

        $this->maxLinks = (int)$links_counter > 1 ? ceil($this->maxLinks / $links_counter) : $this->maxLinks;

        while ($this->temp_links) {

            $temp_links_count = count($this->temp_links);

            $links = $this->temp_links;

            $this->temp_links = [];

            if ($temp_links_count > $this->maxLinks) {

                $links = array_chunk($links, ceil($temp_links_count / $this->maxLinks));

                $count_chunks = count($links);

                for ($i = 0; $i < $count_chunks; $i++) {

                    $this->parsing($links[$i]);

                    unset($links[$i]);

                    if ($links) {

                        foreach ($table_rows as $name => $item) {

                            if ($name === 'temp_links') $table_rows[$name] = json_encode(array_merge(...$links));
                            else $table_rows[$name] = json_encode($this->$name);
                        }

                        $this->model->edit('parsing_data', [
                            'fields' => $table_rows
                        ]);
                    }
                }

            } else {

                $this->parsing($links);
            }

            foreach ($table_rows as $name => $item) {

                $table_rows[$name] = json_encode($this->$name);
            }

            $this->model->edit('parsing_data', [
                'fields' => $table_rows
            ]);
        }

        foreach ($table_rows as $name => $item) {

            $table_rows[$name] = '';
        }

        $this->model->edit('parsing_data', [
            'fields' => $table_rows
        ]);

        if ($this->all_links) {

            foreach ($this->all_links as $key => $link) {

                if (!$this->filter($link) || in_array($link, $this->bad_links)) unset ($this->all_links[$key]);
            }
        }

        $this->createSitemap();

        if ($redirect) {

            !$_SESSION['res']['answer'] && $_SESSION['res']['answer'] = '<div class="success">The sitemap is created</div>';

            $this->redirect();

        } else {

            $this->cancel(1, 'The sitemap is created! ' . count($this->all_links) . ' links', '', true);
        }

    }

    protected function parsing() {

    }

    protected function createLinks() {

    }

    protected function filter() {

    }

    protected function checkParsingTable() {

    }

    protected function cancel() {

    }

    protected function createSitemap() {

    }
}