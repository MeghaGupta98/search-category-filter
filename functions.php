function filter_data()
		{

			$page = sanitize_text_field($_POST['page']);
			$cur_page = $page;
			$page -= 1;
			// Set the number of results to display
			$per_page = 12;
			$pagiper_page = -1;
			$start = $page * $per_page;
			$previous_btn = false;
			$next_btn = true;
			$first_btn = false;
			$last_btn = true;

			$texonomyFilter = [];
			if (!empty($_POST['category'])) {
				foreach ($_POST['category'] as $texonomy => $filter) {
					$texonomyFilter['relation'] = 'OR';
					array_push($texonomyFilter, [
						'taxonomy' => 'product_cat',
						'field' => 'id',
						'terms' => $filter
					]);
				}
			}

			$args = array(
				'post_type'      => 'product',
				'posts_per_page' => $per_page,
				's' =>  $_POST['keyword'] ? $_POST['keyword'] : '',
				'offset' => $start,
				'orderby'   => array(
					'date' => 'ASC',
				),
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => '_price',
						'value' => array($_POST['price_min'], $_POST['price_max']),
						'compare' => 'BETWEEN',
						'type' => 'NUMERIC'
					)
				)
			);

			if (!empty($texonomyFilter)) {
				$args['tax_query'] = $texonomyFilter;
			}

			if ($_POST['onsale'] == 'yes') {
				$arr1 = array(
					'key'           => '_sale_price',
					'value'         => 0,
					'compare'       => '>',
					'type'          => 'numeric'
				);

				if (isset($args['meta_query'])) {
					$args['meta_query'][] = $arr1;
				} else {
					$args['meta_query'] = $arr1;
				}
			}

			if ($_POST['stock'] == 'yes') {
				$arr = array(
					'key' => '_stock_status',
					'value' => 'instock'
				);
				if (isset($args['meta_query'])) {
					$args['meta_query'][] = $arr;
				} else {
					$args['meta_query'] = $arr;
				}
			}

			if (isset($_POST['orderby']) && !empty($_POST['orderby'])) {
				if ($_POST['orderby'] == 'price-asc') {
					$args = array_merge($args, ['orderby'   => 'meta_value_num', 'meta_key'  => '_price', 'order' => 'asc']);
				}
				if ($_POST['orderby'] == 'price-desc') {
					$args = array_merge($args, ['orderby'   => 'meta_value_num', 'meta_key'  => '_price', 'order' => 'desc']);
				}
				if ($_POST['orderby'] == 'popularity') {
					$args = array_merge($args, ['orderby'   => 'meta_value_num', 'meta_key'  => 'total_sales', 'order' => 'desc']);
				}
				if ($_POST['orderby'] == 'date') {
					$args = array_merge($args, ['orderby'   => 'post_date', 'order' => 'desc']);
				}
			}

			$loop = new WP_Query($args);

			unset($args['posts_per_page']);
			unset($args['offset']);
			$args['posts_per_page'] = -1;
			$qry = new WP_Query($args);

			$count = $qry->post_count;

			if ($loop->have_posts()) :
				// echo '<ul class="our-work case-list-work my-3 my-md-4">';
				while ($loop->have_posts()) : $loop->the_post();
					$product = wc_get_product(get_the_ID());
					$display_price = $product->get_price();
					if ($product->is_type('variable')) {
						$variations = $product->get_available_variations();
						$display_price = @$variations[0]['display_price'];
					}
				?>
					<div class="card_prdcts">
						<div class="img_crd">
							<a href="<?php echo get_the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url(); ?>">
								<?php if (!$product->is_in_stock()) { ?><a href="#" class="prdct_sale">Sold Out</a></a><?php } ?>
						<?php if ($product->is_on_sale()) { ?><a href="#" class="prdct_sale">Sale</a></a><?php } ?>
						<button><a href="<?php echo get_the_permalink(); ?>">View Product</a></button>
						</div>
						<div class="crd_text">
							<h5><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h5>
							<p class="rate_prdct">$<?php echo $display_price; ?></p>
						</div>
					</div>
				<?php
				endwhile;
			// echo '</ul>';
			else : ?>
				<div class="our-work case-list-work my-3 my-md-4">

					<div class="col-md-12" style="">
						<h2>No product were found</h2>
					</div>

				</div>
			<?php endif;



			// This is where the magic happens
			if ($count) {
				echo "<input type='hidden' id='cnt' value='" . $count . "'>";
				echo "<input type='hidden' id='offset' value='" . $start . "'>";
				$no_of_paginations = ceil($count / $per_page);

				if ($cur_page >= 7) {
					$start_loop = $cur_page - 3;
					if ($no_of_paginations > $cur_page + 3)
						$end_loop = $cur_page + 3;
					else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
						$start_loop = $no_of_paginations - 6;
						$end_loop = $no_of_paginations;
					} else {
						$end_loop = $no_of_paginations;
					}
				} else {
					$start_loop = 1;
					if ($no_of_paginations > 7)
						$end_loop = 7;
					else
						$end_loop = $no_of_paginations;
				}

				// Pagination Buttons logic    
				$pag_container .= "
				<div class='cvf-universal-pagination'>
					<ul>";

				if ($first_btn && $cur_page > 1) {
					$pag_container .= "<li p='1' class='active'>First</li>";
				} else if ($first_btn) {
					$pag_container .= "<li p='1' class='inactive'>First</li>";
				}

				if ($previous_btn && $cur_page > 1) {
					$pre = $cur_page - 1;
					$pag_container .= "<li p='$pre' class='active'>Previous</li>";
				} else if ($previous_btn) {
					$pag_container .= "<li class='inactive'>Previous</li>";
				}
				for ($i = $start_loop; $i <= $end_loop; $i++) {

					if ($cur_page == $i)
						$pag_container .= "<li p='$i' class = 'selected' >{$i}</li>";
					else
						$pag_container .= "<li p='$i' class='active'>{$i}</li>";
				}

				if ($next_btn && $cur_page < $no_of_paginations) {
					$nex = $cur_page + 1;
					$pag_container .= "<li p='$nex' class='active'>Next</li>";
				} else if ($next_btn) {
					$pag_container .= "<li class='inactive'>Next</li>";
				}

				if ($last_btn && $cur_page < $no_of_paginations) {
					$pag_container .= "<li p='$no_of_paginations' class='active'>Last</li>";
				} else if ($last_btn) {
					$pag_container .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
				}

				$pag_container = $pag_container . "
					</ul>
				</div>";

				// We echo the final output
				echo
				'<div class = "cvf-pagination-content">' . $msg . '</div>' .
					'<div class = "cvf-pagination-nav">' . $pag_container . '</div>';
			}
			die;
		}
		add_action('wp_ajax_filter_data', 'filter_data');    // If called from admin panel
		add_action('wp_ajax_nopriv_filter_data', 'filter_data');
		add_filter('big_image_size_threshold', '__return_false');

		function productonsale()
		{

			$args = array(
				'posts_per_page' => 4,
				'post_type' => 'product',
				'meta_key' => 'total_sales',
				'orderby' => 'meta_value_num',
				'post__in' => array(964, 845, 966, 109)
			);

			$loop = new WP_Query($args);
			$return_string = "";
			$content = '<div class="right_side_bar_home"><div class="product_card_row" id="list-row">';
			while ($loop->have_posts()) : $loop->the_post();
				if (has_post_thumbnail()) :
					$product = wc_get_product(get_the_ID());
					$display_price = $product->get_price();
					if ($product->is_type('variable')) {
						$variations = $product->get_available_variations();
						$display_price = @$variations[0]['display_price'];
					}
					if ($product->is_on_sale()) {
						$salebadge = '<a href="#" class="prdct_sale">Sale</a>';
					} else {
						$salebadge = '';
					}
					$content .= '<div class="card_prdcts">
					  <div class="img_crd">
			  <a href="' . get_the_permalink() . '"><img src="' . get_the_post_thumbnail_url() . '">
			   </a> 
			  <button><a href="' . get_the_permalink() . '">View Product</a></button>
			  </div>
			  <div class="crd_text">
						 <h5><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h5>
						 <p class="rate_prdct">$' . $display_price . '</p>
					  </div>
				   </div>';
				endif;
			endwhile;

			$content .= '</div></div>';
			wp_reset_query();
			return $content;
		}
		function categoryslides()
		{

			$taxonomy     = 'product_cat';
			$orderby      = 'name';
			$show_count   = 0;      // 1 for yes, 0 for no
			$pad_counts   = 0;      // 1 for yes, 0 for no
			$hierarchical = 1;      // 1 for yes, 0 for no  
			$title        = '';
			$empty        = 1;

			$args = array(
				'taxonomy'     => $taxonomy,
				'orderby'      => $orderby,
				'show_count'   => $show_count,
				'pad_counts'   => $pad_counts,
				'hierarchical' => $hierarchical,
				'title_li'     => $title,
				'hide_empty'   => $empty
			);
			$all_categories = get_categories($args);
			$content = '<div class="slick-slider1 slider3">';
			foreach ($all_categories as $cat) {
				if ($cat->category_parent == 0) {
					$category_id = $cat->term_id;
					// print_r($cat);
					$thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);

					// get the image URL
					$image = wp_get_attachment_url($thumbnail_id);

					// print the IMG HTML
					//echo "<img src='{$image}' alt='' width='762' height='365' />";


					$content .= '<div class="slidr_slides">
			<div class="product_card">
			  <div class="prdct_img"><a href="' . get_term_link($cat) . '"><img decoding="async" src="' . $image . '"></a>
			  </div>
			  <div class="prdct_text">
				<h3 class="prdct_name">' . $cat->name . '</h3>
			   
			  </div>
			</div>
		  </div>';
				}
			}

			$content .= '</div>';
			return $content;
		}

		function register_shortcodes()
		{
			add_shortcode('productonsale', 'productonsale');
			add_shortcode('categoryslides', 'categoryslides');
		}
		add_action('init', 'register_shortcodes');
