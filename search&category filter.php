<?php
/* Template Name: PropertyPage 
*/
get_header();

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .card_agent {
        margin-left: 87px;
    }

    h1 {
        padding-top: 175px;
        margin-left: 60px;
        margin-bottom: 60px;
    }

    .post_card_section {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        width: 90%;
        margin: auto;
        padding-top: 50px;
    }

    h4 {
        font-family: rubik;
        font-weight: bold;
    }

    .post_card {
        width: 32%;
        margin-bottom: 30px;
        box-shadow: 0px 1px 18px 0px rgb(0 0 0 / 30%);

    }

    .post_card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card-text {
        padding: 19px;
        text-align: center;
    }

    .card-text p {
        width: 100%;
        color: black;
    }

    * {
        box-sizing: border-box;
    }

    /* Style the search field */
    form.example input[type=text] {
        padding: 10px;
        font-size: 17px;
        border: 1px solid grey;
        float: left;
        width: 80%;
        background: #f1f1f1;
    }

    /* Style the submit button */
    form.example button {
        float: left;
        width: 20%;
        padding: 10px;
        background: #2196F3;
        color: white;
        font-size: 17px;
        border: 1px solid grey;
        border-left: none;
        /* Prevent double borders */
        cursor: pointer;
    }

    form.example button:hover {
        background: #0b7dda;
    }

    /* Clear floats */
    form.example::after {
        content: "";
        clear: both;
        display: table;
    }

    input#keyword {
        width: 26%;
        padding: 10px;
    }

    select#cat {
        width: 26%;
        padding: 10px;
    }
</style>

<h1>PROPERTIES</h1>


<!--- SEARCH BAR --->

<div class="search_bar">
    <form action="/" method="get" autocomplete="off" id="product_search">
        <center> <input type="text" name="s" placeholder="Search Properties..." id="keyword" class="input_search" onkeyup="fetch()">
            <select name="cat" id="cat" onchange="fetch()">
                <option value="">All Categories</option>
                <?php

                // Product category Loop

                $terms = get_terms(array(
                    'taxonomy'   => 'property_category',
                    'hide_empty' => false,
                ));

                // Loop through all category with a foreach loop
                foreach ($terms as $term) {
                    echo '<option value="' . $term->term_id . '"> ' . $term->name . ' </option>';
                }
                ?>
            </select>
        </center>
    </form>
    <br>
    <div class="search_result" id='loader' style='display: none;'>
        <center><img src="http://127.0.0.1/wordpress/wp-content/uploads/2022/11/b4d657e7ef262b88eb5f7ac021edda87.gif" title="loading" height="80" width="100" /></center>
    </div>


</div>

<?php
$params = array('posts_per_page' => 6, 'post_type' => 'properties');
$wc_query = new WP_Query($params);
?>
<div class="search_result" id="datafetch">
    <div class="post_card_section">
        <?php if ($wc_query->have_posts()) : ?>
            <?php while ($wc_query->have_posts()) :
                $wc_query->the_post(); ?>
                <div class="post_card">
                    <?php the_post_thumbnail(); ?>
                    <div class="card-text">
                        <h4><?php the_title(); ?></h4>
                        <?php the_excerpt();  ?>
                    </div>
                </div>

                </form>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else :  ?>
            <li>
                <?php _e('No Products'); ?>
            </li>
        <?php endif; ?>
    </div>
    <?php
    get_footer();
    ?>