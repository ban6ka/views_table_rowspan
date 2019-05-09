<?php

namespace Drupal\views_table_rowspan\Plugin\views\style;

use Drupal\views\Plugin\views\style\Table;
use Drupal\Core\Form\FormStateInterface;

/**
 * Style plugin to merge duplicate row in table.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "table_rowspan",
 *   title = @Translation("Table Rowspan"),
 *   help = @Translation("Merge duplicate rows in group use rowspan attribute."),
 *   theme = "views_view_table",
 *   display_types = {"normal"}
 * )
 */
class TableRowSpan extends Table {

  /**
   * @inheritdoc
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['rowspan'] = ['default' => TRUE];

    return $options;
  }

  /**
   * @inheritdoc
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['rowspan'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Merge rows in table'),
      '#description' => $this->t('Merge rows table that has same value (in a same group) use attribute @url', ['@url' => 'http://www.w3schools.com/tags/att_td_rowspan.asp']),
      '#default_value' => $this->options['rowspan'],
      '#weight' => 0,
    ];
  }

  /**
   * @inheritdoc
   */
  function renderGroupingSets($sets) {
    if (!empty($this->options['grouping']) && !empty($this->options['rowspan'])) {
      $rows = $this->getColSpanRows($sets);
      $sets = [
        [
          'group' => '',
          'rows' => $rows,
        ],
      ];
      // Convert sets to one group.
      $this->options['grouping'] = [];
    }
    return parent::renderGroupingSets($sets);
  }

  /**
   * Convert grouping sets into table rows.
   *
   * @param array $sets
   *   Views grouping sets.
   *
   * @return array
   *   An array of rows in table.
   */
  protected function getColSpanRows($sets, $level = 0) {
    $rows = [];
    $group_field_name = $this->options['grouping'][$level]['field'];
    foreach ($sets as $set) {
      $leaf_rows_index = array_keys($set['rows']);
      $first_index = $leaf_rows_index[0];

      $this->view->rowspan[$group_field_name][$first_index] = $leaf_rows_index;

      foreach ($set['rows'] as $index => $set_row) {
        $rows[$index] = $set_row;
      }
    }
    return $rows;
  }

}
