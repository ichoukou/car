<?php
class Pagination {
	public $total = 0;
	public $page = 1;
	public $limit = 20;
	public $num_links = 8;
	public $url = '';
	public $text_first = '|&lt;';
	public $text_last = '&gt;|';
    #public $text_first = '';
	#public $text_last = '';
	public $text_next = '&gt;';
	public $text_prev = '&lt;';
	public $click_function = '';

	public function render() {
		$total = $this->total;

		if ($this->page < 1) {
			$page = 1;
		} else {
			$page = $this->page;
		}

		if (!(int)$this->limit) {
			$limit = 10;
		} else {
			$limit = $this->limit;
		}

		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);
        $n = round($this->num_links/2); #用于计算是否显示小数点

		$this->url = str_replace('%7Bpage%7D', '{page}', $this->url);
		$output = '<ul class="pagination">';

		if ($page > 1) {
            if (!empty($this->click_function)) {
                $output .= '<li><a href="javascript:void(0)" onclick="'.str_replace('{page}', 1, $this->click_function).'">' . $this->text_first . '</a></li>';
            } else {
                $output .= '<li><a href="' . str_replace('{page}', 1, $this->url) . '">' . $this->text_first . '</a></li>';
            }
            if ($page > $n && $num_pages > $num_links) {
                $point_start = '<li><a href="javascript:void(0)">...</a></li>';
            }
            if (!empty($this->click_function)) {
                $output .= '<li><a href="javascript:void(0)" onclick="'.str_replace('{page}', $page - 1, $this->click_function).'">' .  $this->text_prev  . '</a></li>';
            } else {
                $output .= '<li><a href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_prev . '</a></li>'.$point_start;
            }
		} else {
            $output .= '<li><a href="javascript:void(0)">' . $this->text_prev . '</a></li>';
        }

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);

				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}

				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}

			for ($i = $start; $i <= $end; $i++) {
				if ($page == $i) {
					$output .= '<li class="active"><span>' . $i . '</span></li>';
				} else {
                    if (!empty($this->click_function)) {
                        $output .= '<li><a href="javascript:void(0)" onclick="' . str_replace('{page}', $i, $this->click_function) . '">' . $i . '</a></li>';
                    } else {
                        $output .= '<li><a href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a></li>';
                    }
				}
			}
		}

		if ($page < $num_pages) {
            if ($page <= $num_pages - $n && $num_pages > $num_links) {
                $point_end = '<li><a href="javascript:void(0)">...</a></li>';
            }
            if (!empty($this->click_function)) {
                $output .= $point_end.'<li><a href="javascript:void(0)" onclick="' . str_replace('{page}', 1, $this->click_function) . '">' . $this->text_next . '</a></li>';
            } else {
                $output .= $point_end.'<li><a href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->text_next . '</a></li>';
            }
            if (!empty($this->click_function)) {
                $output .= '<li><a href="javascript:void(0)" onclick="' . str_replace('{page}', $num_pages, $this->click_function) . '">' . $this->text_last . '</a></li>';
            } else {
                $output .= '<li><a href="' . str_replace('{page}', $num_pages, $this->url) . '">' . $this->text_last . '</a></li>';
            }
		} else {
            $output .= '<li><a href="javascript:void(0)">' . $this->text_next . '</a></li>';
        }

		$output .= '</ul>';

		if ($num_pages > 1) {
			return $output;
		} else {
			return '';
		}
	}
}