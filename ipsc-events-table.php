<?php
/**
 * Plugin Name: IPSC Events Table
 * Description: Wyświetla prostą tabelę eventów z tabeli prectec_events.
 * Version: 1.0
 * Author: Bartek
 */

if (!defined('ABSPATH')) {
  $wp_load = __DIR__ . '/wp-load.php';
  if (file_exists($wp_load)) {
    require_once $wp_load;
  } else {
    exit('Brak ustawionego ABSPATH oraz pliku wp-load.php.');
  }
}

$ipsc_table_prefix = 'prec'; // Prefix niestandardowych tabel: ustaw tutaj jedną wartość
$ipsc_locale = 'pl';
if (isset($_GET['lang']) && $_GET['lang'] === 'en') {
  $ipsc_locale = 'en';
} elseif (function_exists('get_locale') && strpos(get_locale(), 'en') === 0) {
  $ipsc_locale = 'en';
}
$ipsc_translation_file = __DIR__ . '/ipsc-events-table-translations.json';
$ipsc_translations = array();
if ($ipsc_locale === 'en' && file_exists($ipsc_translation_file)) {
  $json = file_get_contents($ipsc_translation_file);
  $ipsc_translations = json_decode($json, true) ?: array();
}

function ipsc_t($key, $default = '')
{
  global $ipsc_locale, $ipsc_translations;
  if ($ipsc_locale === 'en' && isset($ipsc_translations[$key])) {
    return $ipsc_translations[$key];
  }
  return $default !== '' ? $default : $key;
}

function ipsc_styles()
{
  return '<style>
#ipsc-events-wrap {
  position: relative;
  font-family: system-ui, -apple-system, sans-serif;
  font-size: 14px;
  line-height: 1.5;
  color: #1a1a2e;
}
#ipsc-events-wrap .ipsc-lang-switcher {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 10;
}
#ipsc-events-wrap .ipsc-lang-switcher select {
  min-width: 100px;
  padding: 6px 10px;
  border: 1px solid #c8d0df;
  border-radius: 6px;
  background: #fff;
  font-size: 13px;
}
#ipsc-events-wrap .ipsc-main-layout {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}
#ipsc-events-wrap .ipsc-filters-sidebar {
  flex: 0 0 280px;
  min-width: 280px;
  min-height: 0;
}
#ipsc-events-wrap .ipsc-table-container {
  flex: 1;
  min-width: 0;
}
#ipsc-events-wrap .ipsc-filter-box {
  background: #f4f6fb;
  border: 1px solid #d0d7e6;
  border-radius: 8px;
  padding: 16px;
  position: sticky;
  top: 20px;
  max-height: calc(100vh - 40px);
  overflow-y: auto;
  overflow-x: hidden;
  box-sizing: border-box;
  min-height: 0;
}
#ipsc-events-wrap .ipsc-filter-section {
  margin-bottom: 12px;
}
#ipsc-events-wrap .ipsc-filter-section strong {
  display: block;
  margin-bottom: 6px;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: .05em;
  color: #4a5568;
}
#ipsc-events-wrap .ipsc-selector {
  width: 100%;
}
#ipsc-events-wrap .ipsc-selector select {
  width: 100%;
  min-height: 100px;
  padding: 6px 8px;
  border: 1px solid #c8d0df;
  border-radius: 6px;
  background: #fff;
  font-size: 13px;
  font-family: inherit;
}
#ipsc-events-wrap .ipsc-selector select:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
#ipsc-events-wrap .ipsc-selector option {
  padding: 4px 8px;
}
#ipsc-events-wrap .ipsc-selector option:checked {
  background: #2563eb;
  color: #fff;
}
#ipsc-events-wrap .ipsc-filter-actions {
  margin-top: 12px;
  display: flex;
  gap: 10px;
  align-items: center;
}
#ipsc-events-wrap .ipsc-btn {
  display: inline-block;
  padding: 7px 20px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  border: none;
  transition: background .15s;
}
#ipsc-events-wrap .ipsc-btn-primary {
  background: #2563eb;
  color: #fff;
}
#ipsc-events-wrap .ipsc-btn-primary:hover {
  background: #1d4ed8;
}
#ipsc-events-wrap .ipsc-btn-secondary {
  background: #e5e7eb;
  color: #374151;
}
#ipsc-events-wrap .ipsc-btn-secondary:hover {
  background: #d1d5db;
}
#ipsc-events-wrap .ipsc-table-wrap {
  overflow-x: auto;
}
#ipsc-events-wrap table.ipsc-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
#ipsc-events-wrap table.ipsc-table th {
  background: #1e3a5f;
  color: #fff;
  padding: 10px 12px;
  text-align: left;
  white-space: nowrap;
  font-weight: 600;
  letter-spacing: .03em;
}
#ipsc-events-wrap table.ipsc-table td {
  padding: 8px 12px;
  vertical-align: top;
  border-bottom: 1px solid #e2e8f0;
}
#ipsc-events-wrap table.ipsc-table tbody tr:nth-child(even) td {
  background: #f8fafc;
}
#ipsc-events-wrap table.ipsc-table tbody tr:hover td {
  background: #eef4ff;
}
#ipsc-events-wrap .ipsc-date {
  white-space: nowrap;
  color: #374151;
}
#ipsc-events-wrap .ipsc-title {
  font-weight: 600;
  color: #1e3a5f;
}
#ipsc-events-wrap .ipsc-tag {
  display: inline-block;
  background: #dbeafe;
  color: #1e40af;
  border-radius: 4px;
  padding: 1px 6px;
  font-size: 11px;
  margin: 1px;
}
#ipsc-events-wrap .ipsc-cat {
  display: inline-block;
  background: #dcfce7;
  color: #166534;
  border-radius: 4px;
  padding: 1px 6px;
  font-size: 11px;
  margin: 1px;
}
#ipsc-events-wrap a.ipsc-url {
  color: #2563eb;
  word-break: break-all;
}
#ipsc-events-wrap details summary {
  cursor: pointer;
  color: #2563eb;
  font-size: 12px;
  user-select: none;
}
#ipsc-events-wrap details[open] summary {
  margin-bottom: 8px;
}
#ipsc-events-wrap .ipsc-content-wrap {
  max-width: 560px;
  overflow: auto;
  font-size: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 8px;
  background: #fff;
}
#ipsc-events-wrap .ipsc-content-wrap table {
  border-collapse: collapse;
  width: 100%;
}
#ipsc-events-wrap .ipsc-content-wrap td {
  padding: 4px 6px;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: top;
}
#ipsc-events-wrap .ipsc-content-wrap td:first-child {
  font-weight: 600;
  white-space: nowrap;
  width: 35%;
  color: #4a5568;
}
#ipsc-events-wrap .ipsc-no-results {
  padding: 20px;
  text-align: center;
  color: #6b7280;
  background: #f9fafb;
  border-radius: 8px;
  border: 1px dashed #d1d5db;
}
#ipsc-events-wrap table.ipsc-table tbody tr[data-has-content] {
  cursor: pointer;
}
/* Modal */
#ipsc-modal-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.55);
  z-index: 99999;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
#ipsc-modal-overlay.ipsc-modal-open {
  display: flex;
}
#ipsc-modal-box {
  background: #fff;
  border-radius: 10px;
  max-width: 760px;
  width: 100%;
  max-height: 88vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,.3);
  overflow: hidden;
}
#ipsc-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 20px;
  background: #1e3a5f;
  color: #fff;
  flex-shrink: 0;
}
#ipsc-modal-title {
  font-size: 16px;
  font-weight: 700;
  margin: 0;
}
#ipsc-modal-close {
  background: none;
  border: none;
  color: #fff;
  font-size: 22px;
  cursor: pointer;
  line-height: 1;
  padding: 0 4px;
  opacity: .8;
}
#ipsc-modal-close:hover { opacity: 1; }
#ipsc-modal-body {
  padding: 16px 20px;
  overflow-y: auto;
  font-size: 13px;
  line-height: 1.6;
}
/* General content formatting */
#ipsc-modal-body p {
  margin: 0 0 8px;
}
#ipsc-modal-body ul,
#ipsc-modal-body ol {
  margin: 0 0 8px;
  padding-left: 20px;
}
#ipsc-modal-body li {
  margin-bottom: 3px;
}
#ipsc-modal-body b,
#ipsc-modal-body strong {
  font-weight: 700;
}
#ipsc-modal-body h1,
#ipsc-modal-body h2,
#ipsc-modal-body h3 {
  margin: 12px 0 6px;
  font-weight: 700;
  color: #1e3a5f;
}
#ipsc-modal-body a {
  color: #2563eb;
  word-break: break-all;
}
#ipsc-modal-body img {
  max-width: 100%;
  height: auto;
  display: inline-block;
}
/* Generic table (no class) */
#ipsc-modal-body table:not(.lined) {
  border-collapse: collapse;
  width: 100%;
  margin: 8px 0;
}
#ipsc-modal-body table:not(.lined) td,
#ipsc-modal-body table:not(.lined) th {
  padding: 5px 8px;
  border-bottom: 1px solid #e2e8f0;
  vertical-align: top;
}
#ipsc-modal-body table:not(.lined) td:first-child,
#ipsc-modal-body table:not(.lined) th:first-child {
  font-weight: 600;
  white-space: nowrap;
  width: 32%;
  color: #4a5568;
}
/* Styles from ipscmatch.de content */
#ipsc-modal-body table.lined {
  border-collapse: collapse;
  width: 100%;
  margin-top: 8px;
}
#ipsc-modal-body table.lined td {
  padding: 6px 10px;
  border: 1px solid #e2e8f0;
  vertical-align: top;
}
#ipsc-modal-body table.lined td:first-child {
  font-weight: 600;
  white-space: nowrap;
  width: 30%;
  background: #f8fafc;
  color: #1e3a5f;
}
#ipsc-modal-body table.lined tr:hover td {
  background: #f0f7ff;
}
#ipsc-modal-body .match_deadline_good {
  color: #15803d;
  font-weight: 600;
}
#ipsc-modal-body .match_deadline_bad {
  color: #dc2626;
  font-weight: 600;
}
#ipsc-modal-body .match_status_final {
  display: inline-block;
  background: #dcfce7;
  color: #166534;
  border: 1px solid #86efac;
  border-radius: 4px;
  padding: 2px 8px;
  font-size: 12px;
  font-weight: 600;
}
</style>';
}

function ipsc_events_table()
{
  global $wpdb, $ipsc_table_prefix, $ipsc_locale;

  $tbl_events = $ipsc_table_prefix . 'tec_events';
  $tbl_posts = $wpdb->posts;
  $tbl_tr = $wpdb->term_relationships;
  $tbl_tt = $wpdb->term_taxonomy;
  $tbl_terms = $wpdb->terms;

  // Sprawdź istnienie tabeli eventów
  $check = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $tbl_events));
  if (!$check) {
    return '<p>Brak tabeli <code>' . esc_html($tbl_events) . '</code> w bazie.</p>';
  }

  // --- Pobierz dostępne tagi (post_tag) do filtra ---
  $available_tags = $wpdb->get_results(
    "SELECT t.term_id, t.name, t.slug
     FROM {$tbl_terms} t
     JOIN {$tbl_tt} tt ON tt.term_id = t.term_id
     WHERE tt.taxonomy = 'post_tag'
     ORDER BY t.name ASC",
    ARRAY_A
  );

  // --- Pobierz dostępne kategorie (tribe_events_cat) do filtra ---
  $available_cats = $wpdb->get_results(
    "SELECT t.term_id, t.name, t.slug
     FROM {$tbl_terms} t
     JOIN {$tbl_tt} tt ON tt.term_id = t.term_id
     WHERE tt.taxonomy = 'tribe_events_cat'
     ORDER BY t.name ASC",
    ARRAY_A
  );

  // Slugi geograficzne – województwa + zagranica
  $geo_slugs = array(
    'dolnoslaskie',
    'kujawsko-pomorskie',
    'lubelskie',
    'lubuskie',
    'lodzkie',
    'malopolskie',
    'mazowieckie',
    'opolskie',
    'podkarpackie',
    'podlaskie',
    'pomorskie',
    'slaskie',
    'swietokrzyskie',
    'warminsko-mazurskie',
    'wielkopolskie',
    'zachodniopomorskie',
    'zagranica',
  );

  $available_location_cats = array();
  $available_other_cats = array();
  foreach ($available_cats as $cat) {
    if (in_array($cat['slug'], $geo_slugs, true)) {
      $available_location_cats[] = $cat;
    } else {
      $available_other_cats[] = $cat;
    }
  }

  $location_slugs_sql = "'" . implode("','", array_map('esc_sql', $geo_slugs)) . "'";

  $default_date_from = current_time('Y-m-d');
  $default_date_to = $wpdb->get_var("SELECT MAX(DATE(end_date)) FROM {$tbl_events}");
  if (empty($default_date_to)) {
    $default_date_to = $default_date_from;
  }

  // --- Odczytaj wyszukiwanie z GET ---
  $search_term = '';
  if (!empty($_GET['ipsc_search'])) {
    $search_term = sanitize_text_field($_GET['ipsc_search']);
  }

  // --- Odczytaj zakres dat z GET ---
  $date_from = '';
  $date_to = '';
  if (isset($_GET['ipsc_date_from'])) {
    $date_from = sanitize_text_field($_GET['ipsc_date_from']);
  }
  if (isset($_GET['ipsc_date_to'])) {
    $date_to = sanitize_text_field($_GET['ipsc_date_to']);
  }

  $display_date_from = $date_from !== '' ? $date_from : $default_date_from;
  $display_date_to = $date_to !== '' ? $date_to : $default_date_to;

  $filter_date_from = $date_from !== '' ? $date_from : $default_date_from;
  $filter_date_to = $date_to !== '' ? $date_to : $default_date_to;

  $page_size = 30;
  $current_page = 1;
  if (!empty($_GET['ipsc_page'])) {
    $current_page = max(1, intval($_GET['ipsc_page']));
  }
  $offset = ($current_page - 1) * $page_size;

  // --- Odczytaj wybrane tagi z GET (sanityzacja) ---
  $selected_slugs = array();
  if (!empty($_GET['ipsc_tags']) && is_array($_GET['ipsc_tags'])) {
    foreach ($_GET['ipsc_tags'] as $raw) {
      $slug = sanitize_key($raw);
      if ($slug !== '') {
        $selected_slugs[] = $slug;
      }
    }
  }

  // --- Odczytaj wybrane kategorie z GET (sanityzacja) ---
  $selected_cats = array();
  if (!empty($_GET['ipsc_cats']) && is_array($_GET['ipsc_cats'])) {
    foreach ($_GET['ipsc_cats'] as $raw) {
      $slug = sanitize_key($raw);
      if ($slug !== '') {
        $selected_cats[] = $slug;
      }
    }
  }

  // --- Odczytaj wybrane lokalizacje (województwa/zagranica) z GET ---
  $selected_locations = array();
  if (!empty($_GET['ipsc_location']) && is_array($_GET['ipsc_location'])) {
    foreach ($_GET['ipsc_location'] as $raw) {
      $slug = sanitize_key($raw);
      if ($slug !== '') {
        $selected_locations[] = $slug;
      }
    }
  }

  // --- Buduj warunek filtra ---
  $where_filter = "WHERE 1=1";

  if (!empty($selected_slugs)) {
    $placeholders = implode(', ', array_fill(0, count($selected_slugs), '%s'));
    $where_filter .= $wpdb->prepare(
      " AND e.post_id IN (
          SELECT tr2.object_id
          FROM {$tbl_tr}  tr2
          JOIN {$tbl_tt}  tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
          JOIN {$tbl_terms} t2 ON t2.term_id = tt2.term_id
          WHERE tt2.taxonomy = 'post_tag'
            AND t2.slug IN ({$placeholders})
          GROUP BY tr2.object_id
          HAVING COUNT(DISTINCT t2.slug) = %d
        )",
      array_merge($selected_slugs, array(count($selected_slugs)))
    );
  }

  if (!empty($selected_cats)) {
    $placeholders = implode(', ', array_fill(0, count($selected_cats), '%s'));
    $where_filter .= $wpdb->prepare(
      " AND e.post_id IN (
          SELECT tr2.object_id
          FROM {$tbl_tr}  tr2
          JOIN {$tbl_tt}  tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
          JOIN {$tbl_terms} t2 ON t2.term_id = tt2.term_id
          WHERE tt2.taxonomy = 'tribe_events_cat'
            AND t2.slug IN ({$placeholders})
          GROUP BY tr2.object_id
          HAVING COUNT(DISTINCT t2.slug) = %d
        )",
      array_merge($selected_cats, array(count($selected_cats)))
    );
  }

  // Lokalizacja – OR (event musi być w przynajmniej jednej wybranej lokalizacji)
  if (!empty($selected_locations)) {
    $placeholders = implode(', ', array_fill(0, count($selected_locations), '%s'));
    $where_filter .= $wpdb->prepare(
      " AND e.post_id IN (
          SELECT tr2.object_id
          FROM {$tbl_tr}  tr2
          JOIN {$tbl_tt}  tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
          JOIN {$tbl_terms} t2 ON t2.term_id = tt2.term_id
          WHERE tt2.taxonomy = 'tribe_events_cat'
            AND t2.slug IN ({$placeholders})
        )",
      $selected_locations
    );
  }

  if ($search_term !== '') {
    $like = '%' . $wpdb->esc_like($search_term) . '%';
    $where_filter .= $wpdb->prepare(
      " AND (
          p.post_title LIKE %s
          OR p.post_content LIKE %s
          OR e.start_date LIKE %s
          OR e.end_date LIKE %s
          OR e.timezone LIKE %s
          OR EXISTS (
            SELECT 1
            FROM {$tbl_tr} tr2
            JOIN {$tbl_tt} tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
            JOIN {$tbl_terms} t2 ON t2.term_id = tt2.term_id
            WHERE tr2.object_id = e.post_id
              AND t2.name LIKE %s
          )
        )",
      array($like, $like, $like, $like, $like, $like)
    );
  }

  if ($filter_date_from !== '') {
    $where_filter .= $wpdb->prepare(" AND DATE(e.start_date) >= %s", $filter_date_from);
  }
  if ($filter_date_to !== '') {
    $where_filter .= $wpdb->prepare(" AND DATE(e.start_date) <= %s", $filter_date_to);
  }

  $total_items = $wpdb->get_var(
    "SELECT COUNT(DISTINCT e.event_id) FROM {$tbl_events} e
     JOIN {$tbl_posts} p ON p.ID = e.post_id AND p.post_status = 'publish'
     LEFT JOIN {$tbl_tr} tr ON tr.object_id = e.post_id
     LEFT JOIN {$tbl_tt} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
     LEFT JOIN {$tbl_terms} t ON t.term_id = tt.term_id
     LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = e.post_id AND pm.meta_key = '_EventURL'
     {$where_filter}"
  );
  $total_pages = max(1, (int) ceil($total_items / $page_size));
  if ($current_page > $total_pages) {
    $current_page = $total_pages;
    $offset = ($current_page - 1) * $page_size;
  }

  // --- Główne zapytanie ---
  $query = $wpdb->prepare(
    "SELECT
       e.event_id,
       e.post_id,
       p.post_title                                                              AS event_name,
       e.start_date,
       e.end_date,
       GROUP_CONCAT(DISTINCT CASE WHEN tt.taxonomy='tribe_events_cat' AND t.slug IN ({$location_slugs_sql}) THEN t.name END
                    ORDER BY t.name SEPARATOR ', ')                              AS lokalizacja,
       e.timezone,
       GROUP_CONCAT(DISTINCT CASE WHEN tt.taxonomy='tribe_events_cat' THEN t.name END
                    ORDER BY t.name SEPARATOR ', ')                              AS kategorie,
       GROUP_CONCAT(DISTINCT CASE WHEN tt.taxonomy='post_tag' THEN t.name END
                    ORDER BY t.name SEPARATOR ', ')                              AS tagi,
       MAX(CASE WHEN pm.meta_key='_EventURL'  THEN pm.meta_value END)           AS url,
       p.post_content                                                            AS post_content
     FROM {$tbl_events} e
     JOIN {$tbl_posts} p
       ON p.ID = e.post_id AND p.post_status = 'publish'
     LEFT JOIN {$tbl_tr}    tr ON tr.object_id        = e.post_id
     LEFT JOIN {$tbl_tt}    tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
     LEFT JOIN {$tbl_terms} t  ON t.term_id           = tt.term_id
     LEFT JOIN {$wpdb->postmeta} pm
       ON pm.post_id = e.post_id AND pm.meta_key = '_EventURL'
     {$where_filter}
     GROUP BY e.event_id, e.post_id, p.post_title, e.start_date, e.end_date, e.timezone, p.post_content
     ORDER BY e.start_date ASC
     LIMIT %d OFFSET %d",
    $page_size,
    $offset
  );
  $events = $wpdb->get_results($query, ARRAY_A);

  // --- CSS + wrapper ---
  $form_action = esc_url(strtok($_SERVER['REQUEST_URI'], '?'));
  $html = ipsc_styles();
  $html .= '<div id="ipsc-events-wrap">';
  $html .= '<div class="ipsc-lang-switcher"><form method="get" action="' . $form_action . '" style="margin:0;">';
  foreach ($_GET as $key => $val) {
    if ($key === 'lang') {
      continue;
    }
    $key = sanitize_key($key);
    if (is_array($val)) {
      foreach ($val as $v) {
        $html .= '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($v) . '">';
      }
    } else {
      $html .= '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '">';
    }
  }
  $html .= '<select name="lang" onchange="this.form.submit()">';
  $html .= '<option value="pl"' . ($ipsc_locale === 'pl' ? ' selected' : '') . '>🇵🇱 PL</option>';
  $html .= '<option value="en"' . ($ipsc_locale === 'en' ? ' selected' : '') . '>🇬🇧 EN</option>';
  $html .= '</select></form></div>';
  $html .= '<div class="ipsc-main-layout">';
  $html .= '<div class="ipsc-filters-sidebar">';

  // --- Formularz filtra ---
  $form_action = esc_url(strtok($_SERVER['REQUEST_URI'], '?'));
  $html .= '<form method="get" action="' . $form_action . '" class="ipsc-filter-box">';

  // przekaż query-string WordPress (page, p, itp.) jako hidden inputs
  foreach ($_GET as $key => $val) {
    if ($key === 'ipsc_tags' || $key === 'ipsc_cats' || $key === 'ipsc_location' || $key === 'ipsc_search' || $key === 'ipsc_date_from' || $key === 'ipsc_date_to') {
      continue;
    }
    $key = sanitize_key($key);
    if (is_array($val)) {
      foreach ($val as $v) {
        $html .= '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($v) . '">';
      }
    } else {
      $html .= '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '">';
    }
  }

  $html .= '<div class="ipsc-filter-section"><strong>' . esc_html(ipsc_t('search_label', 'Wyszukaj')) . '</strong>';
  $html .= '<input type="search" name="ipsc_search" value="' . esc_attr($search_term) . '" placeholder="' . esc_attr(ipsc_t('search_placeholder', 'Wyszukaj po nazwie, opisie, tagach i lokalizacji')) . '" style="width:100%;padding:8px 10px;border:1px solid #c8d0df;border-radius:6px;font-size:14px;box-sizing:border-box;">';
  $html .= '</div>';

  $html .= '<div class="ipsc-filter-section"><strong>' . esc_html(ipsc_t('date_label', 'Data')) . '</strong><div style="display:grid;gap:8px;">';
  $html .= '<input type="date" name="ipsc_date_from" value="' . esc_attr($display_date_from) . '" style="width:100%;padding:8px 10px;border:1px solid #c8d0df;border-radius:6px;font-size:14px;box-sizing:border-box;">';
  $html .= '<input type="date" name="ipsc_date_to" value="' . esc_attr($display_date_to) . '" style="width:100%;padding:8px 10px;border:1px solid #c8d0df;border-radius:6px;font-size:14px;box-sizing:border-box;">';
  $html .= '</div></div>';

  if (!empty($available_location_cats)) {
    $html .= '<div class="ipsc-filter-section"><strong>' . esc_html(ipsc_t('location_filter_label', 'Województwo / Zagranica')) . '</strong><div class="ipsc-selector">';
    $html .= '<select name="ipsc_location[]" multiple>';
    foreach ($available_location_cats as $cat) {
      $selected = in_array($cat['slug'], $selected_locations, true) ? ' selected' : '';
      $html .= '<option value="' . esc_attr($cat['slug']) . '"' . $selected . '>' . esc_html($cat['name']) . '</option>';
    }
    $html .= '</select></div></div>';
  }

  $html .= '<div class="ipsc-filter-section"><strong>' . esc_html(ipsc_t('tags_filter_label', 'Filtruj po tagach')) . '</strong><div class="ipsc-selector">';
  $html .= '<select name="ipsc_tags[]" multiple>';
  foreach ($available_tags as $tag) {
    $selected = in_array($tag['slug'], $selected_slugs, true) ? ' selected' : '';
    $html .= '<option value="' . esc_attr($tag['slug']) . '"' . $selected . '>' . esc_html($tag['name']) . '</option>';
  }
  $html .= '</select></div></div>';

  $html .= '<div class="ipsc-filter-actions">';
  $html .= '<button type="submit" class="ipsc-btn ipsc-btn-primary">' . esc_html(ipsc_t('apply_filters', 'Zastosuj filtry')) . '</button>';
  $html .= '<a href="' . $form_action . '" class="ipsc-btn ipsc-btn-secondary">' . esc_html(ipsc_t('clear_filters', 'Wyczyść filtry')) . '</a>';
  $html .= '</div>';
  $html .= '</form>';
  $html .= '</div>'; // close .ipsc-filters-sidebar
  $html .= '<div class="ipsc-table-container">';

  // --- Tabela wyników ---
  if (empty($events)) {
    $html .= '<p class="ipsc-no-results">' . esc_html(ipsc_t('no_results', 'Brak eventów spełniających wybrane kryteria.')) . '</p>';
    $html .= '</div>'; // close .ipsc-table-container
    $html .= '</div>'; // close .ipsc-main-layout
    $html .= '</div>'; // close #ipsc-events-wrap
    return $html;
  }

  $cols = array(
    'event_name' => ipsc_t('event_name', 'Nazwa eventu'),
    'start_date' => ipsc_t('start_date', 'Data startu'),
    'end_date' => ipsc_t('end_date', 'Data końca'),
    'lokalizacja' => ipsc_t('location', 'Lokalizacja'),
    'tagi' => ipsc_t('tags', 'Tagi'),
    'url' => ipsc_t('url', 'URL'),
  );

  $html .= '<div class="ipsc-table-wrap"><table class="ipsc-table">';
  $html .= '<thead><tr>';
  foreach ($cols as $label) {
    $html .= '<th>' . esc_html($label) . '</th>';
  }
  $html .= '</tr></thead><tbody>';

  foreach ($events as $event) {
    $has_content = !empty($event['post_content']);
    $data_title = esc_attr($event['event_name']);
    $event_id = (int) $event['event_id'];
    $row_attrs = $has_content
      ? ' data-has-content="1" data-event-id="' . $event_id . '" data-title="' . $data_title . '" title="' . esc_attr(ipsc_t('click_for_details', 'Kliknij, aby zobaczyć szczegóły')) . '"'
      : '';
    $html .= '<tr' . $row_attrs . '>';
    foreach (array_keys($cols) as $field) {
      $value = isset($event[$field]) ? $event[$field] : '';

      if ($field === 'event_name') {
        $html .= '<td class="ipsc-title">' . esc_html($value) . '</td>';

      } elseif (in_array($field, array('start_date', 'end_date'), true)) {
        $parsed = strtotime($value);
        $formatted = $parsed !== false ? date_i18n('Y-m-d', $parsed) : esc_html($value);
        $html .= '<td class="ipsc-date">' . esc_html($formatted) . '</td>';

      } elseif ($field === 'lokalizacja') {
        $location_text = $value !== '' ? mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') : ipsc_t('no_location', 'brak lokalizacji');
        $html .= '<td>' . esc_html($location_text) . '</td>';

      } elseif ($field === 'tagi') {
        $tags_out = '';
        foreach (array_filter(array_map('trim', explode(',', $value))) as $t) {
          $tags_out .= '<span class="ipsc-tag">' . esc_html($t) . '</span>';
        }
        $html .= '<td>' . $tags_out . '</td>';

      } elseif ($field === 'url') {
        if ($value !== '') {
          $html .= '<td><a class="ipsc-url" href="' . esc_url($value) . '" target="_blank" rel="noopener noreferrer">' . esc_html($value) . '</a></td>';
        } else {
          $html .= '<td>—</td>';
        }

      } else {
        $html .= '<td>' . esc_html($value) . '</td>';
      }
    }
    $html .= '</tr>';
  }

  $html .= '</tbody></table></div>';

  if ($total_pages > 1) {
    $query_args = array();
    foreach ($_GET as $key => $val) {
      if ($key === 'ipsc_page') {
        continue;
      }
      $query_args[$key] = $val;
    }
    $html .= '<div class="ipsc-pagination" style="margin:16px 0; display:flex; flex-wrap:wrap; gap:8px; align-items:center;">';
    if ($current_page > 1) {
      $query_args['ipsc_page'] = $current_page - 1;
      $html .= '<a class="ipsc-btn ipsc-btn-secondary" href="' . esc_url(add_query_arg($query_args, $form_action)) . '">' . esc_html(ipsc_t('previous', 'Poprzednia')) . '</a>';
    }
    for ($page = 1; $page <= $total_pages; $page++) {
      $query_args['ipsc_page'] = $page;
      $current = $page === $current_page ? 'background:#1d4ed8;color:#fff;' : '';
      $html .= '<a class="ipsc-btn ipsc-btn-secondary" style="padding:6px 12px; font-size:13px; ' . $current . '" href="' . esc_url(add_query_arg($query_args, $form_action)) . '">' . esc_html($page) . '</a>';
    }
    if ($current_page < $total_pages) {
      $query_args['ipsc_page'] = $current_page + 1;
      $html .= '<a class="ipsc-btn ipsc-btn-secondary" href="' . esc_url(add_query_arg($query_args, $form_action)) . '">' . esc_html(ipsc_t('next', 'Następna')) . '</a>';
    }
    $html .= '</div>';
  }

  $html .= '</div>'; // close .ipsc-table-container
  $html .= '</div>'; // close .ipsc-main-layout

  // --- Ukryte divy z treścią eventów ---
  $html .= '<div style="display:none" aria-hidden="true">';
  foreach ($events as $event) {
    if (!empty($event['post_content'])) {
      $event_id = (int) $event['event_id'];
      // Treść blokowa (tabela/div) - renderuj jako HTML bez modyfikacji.
      // Treść z inline tagami lub czysty tekst - zastosuj nl2br.
      $is_block_html = preg_match('/<(table|thead|tbody|tr|td|div|ul|ol|li|h[1-6]|blockquote)[\s>]/i', $event['post_content']);
      $content = wp_kses_post($event['post_content']);
      if (!$is_block_html) {
        $content = nl2br($content);
      }
      $html .= '<div id="ipsc-content-' . $event_id . '">' . $content . '</div>';
    }
  }
  $html .= '</div>';

  // --- Modal ---
  $html .= '<div id="ipsc-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="ipsc-modal-title">';
  $html .= '<div id="ipsc-modal-box">';
  $html .= '<div id="ipsc-modal-header">';
  $html .= '<h3 id="ipsc-modal-title"></h3>';
  $html .= '<button id="ipsc-modal-close" aria-label="Zamknij">&times;</button>';
  $html .= '</div>';
  $html .= '<div id="ipsc-modal-body"></div>';
  $html .= '</div></div>';

  // --- JS ---
  $html .= "<script>
(function(){
  var overlay  = document.getElementById('ipsc-modal-overlay');
  var title    = document.getElementById('ipsc-modal-title');
  var body     = document.getElementById('ipsc-modal-body');
  var closeBtn = document.getElementById('ipsc-modal-close');

  document.querySelectorAll('#ipsc-events-wrap tr[data-has-content]').forEach(function(row){
    row.addEventListener('click', function(e){
      if (e.target.closest('a')) return;
      var src = document.getElementById('ipsc-content-' + row.dataset.eventId);
      if (!src) return;
      title.textContent = row.dataset.title;
      body.innerHTML = src.innerHTML;
      overlay.classList.add('ipsc-modal-open');
      document.body.style.overflow = 'hidden';
    });
  });

  function closeModal(){
    overlay.classList.remove('ipsc-modal-open');
    document.body.style.overflow = '';
  }
  closeBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', function(e){
    if (e.target === overlay) closeModal();
  });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeModal();
  });
})();
</script>";

  $html .= '</div>'; // close #ipsc-events-wrap

  return $html;
}

function ipsc_events_table_shortcode()
{
  return ipsc_events_table();
}
add_shortcode('ipsc_events_table', 'ipsc_events_table_shortcode');

if (!empty($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
  echo '<!doctype html><html><head><meta charset="utf-8"><title>IPSC events</title></head><body>';
  echo ipsc_events_table();
  echo '</body></html>';
  exit;
}
