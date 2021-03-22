<?php
/**
 * Override standard class WP_List_Table. It's needed to show custom layout with post entities
 */
namespace Tool\Admin;

class Entities_Table extends \WP_List_Table
{
  /**
   * Order by fields
   * @var string[]
   */
  private $order_by_list = [
    'title',
    'author',
    'post_status'
  ];

  /**
   * Entities per page displayed on table layout
   * @var int
   */
  public $entities_per_page = 20;

  public function __construct()
  {
    $args = [
      'singular' => __('entity', 'someexample.com'),
      'plural' => __('entities', 'someexample.com'),
      'ajax' => true
    ];
    parent::__construct($args);
  }

  public function get_order_by_default_list()
  {
    return $this->order_by_list;
  }

  /**
   * Override default columns
   * @param object $item
   * @param string $column_name
   * @return string|true|void
   */
  public function column_default($item, $column_name)
  {
    switch($column_name) {
      case 'title':
      case 'author':
      case 'status':
      case 'slug':
        return $item[$column_name];
      default:
        if (WP_DEBUG) {
          return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        } else {
          wp_die(
            __('Table render failed', 'someexample.com')
          );
        }

    }
  }

  /**
   * Override default sort by DESC
   * @return array|array[]
   */
  public function get_sortable_columns()
  {
    return [
      'title'  => ['title', false],
      'author'  => ['author', false],
    ];
  }

  /**
   * Replace default columns
   * @return array
   */
  public function get_columns()
  {
    return [
      'cb' => '<input type="checkbox" />',
      'title' => __('Title', 'someexample.com'),
      'slug' => __('Slug', 'someexample.com'),
      'author' => __('Author', 'someexample.com'),
      'status' => __('Status', 'someexample.com'),
    ];
  }

  /**
   * Replace default checkbox in row
   * @param object $item
   * @return string|void
   */
  public function column_cb($item) {
    return sprintf(
      '<input type="checkbox" name="entity[]" value="%s" />', $item['ID']
    );
  }

  /**
   * Retrieve entities from DB
   * @return array
   */
  public function get_entities() {
    extract($this->prepare_query_args());
    $current_page = $this->get_pagenum();
    $args = [
      'post_type' => $post_type,
      'post_status' => $post_status,
      'orderby' => $order_by,
      'order' => $order,
      'posts_per_page' => $this->entities_per_page,
      'paged' => $current_page
    ];

    if (!empty($_GET['clone-tool-search-field'])) {
      $args['s'] = esc_sql($_GET['clone-tool-search-field']);
    }

    $entities = new \WP_Query($args);

    return [
      'posts' => $this->parse_entities($entities->posts),
      'found_posts' => $entities->found_posts
    ];
  }

  /**
   * Check and prepare query vars
   * @return array
   */
  public function prepare_query_args()
  {
    $post_types_list = Data_View::get_post_types_list();
    $post_statuses_list = array_keys(Data_View::get_statuses_list());

    $post_type = Helper::is_valid_item($_GET['clone-tool-post-type'], $post_types_list, 'post');
    $post_status = Helper::is_valid_item($_GET['clone-post-status'], $post_statuses_list, 'publish');
    $order_by = Helper::is_valid_item($_GET['orderby'], $this->get_order_by_default_list(), 'date');
    $order = Helper::is_valid_item(strtoupper($_GET['order']), ['ASC', 'DESC'], 'DESC');

    return [
      'post_type' => $post_type,
      'post_status' => $post_status,
      'order' => $order,
      'order_by' => $order_by
    ];
  }

  /**
   * Prepare entities for table layout
   * @param array $entities
   * @return array
   */
  public function parse_entities($entities = [])
  {
    $parsed_entities = [];
    if (!empty($entities)) {
      foreach ($entities as $entity) {
        $author = get_the_author_meta('nickname', $entity->post_author);
        $parsed_entities[] = [
          'ID' => $entity->ID,
          'title' => $entity->post_title,
          'slug' => $entity->post_name,
          'author' => $author,
          'status' => $entity->post_status,
          'type' => $entity->post_type
        ];
      }
    }

    return $parsed_entities;
  }

  /**
   * Override default prepare items logic. Retreive and parse data with get_entities
   */
  public function prepare_items() {
    $columns  = $this->get_columns();
    $hidden   = [];
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = [$columns, $hidden, $sortable];
    $data = $this->get_entities();

    $total_items = $data['found_posts'];


    $this->set_pagination_args([
      'total_items' => $total_items,
      'per_page'  => $this->entities_per_page
    ]);
    $this->items = $data['posts'];
  }
}