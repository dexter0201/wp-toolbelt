"use strict";

var registerBlockType = wp.blocks.registerBlockType;
var createElement = wp.element.createElement;
var InspectorControls = wp.blockEditor.InspectorControls;
var _wp$components = wp.components,
    RangeControl = _wp$components.RangeControl,
    SelectControl = _wp$components.SelectControl,
    PanelBody = _wp$components.PanelBody,
    CheckboxControl = _wp$components.CheckboxControl,
    ServerSideRender = _wp$components.ServerSideRender;
var __ = wp.i18n.__;
registerBlockType('toolbelt/portfolio', {
  title: __('Portfolio', 'wp-toolbelt'),
  icon: 'portfolio',
  description: __('Display a grid of Toolbelt Projects.', 'wp-toolbelt'),
  category: 'common',
  keywords: [__('projects', 'wp-toolbelt'), __('toolbelt', 'wp-toolbelt')],
  supports: {
    align: ['full', 'wide']
  },
  attributes: {
    rows: {
      "default": 2
    },
    columns: {
      "default": 2
    },
    orderby: {
      "default": 'date'
    },
    categories: {
      "default": ''
    }
  },
  edit: function edit(props) {
    var attributes = props.attributes;
    var setAttributes = props.setAttributes;
    var categoriesArray = [];

    if (attributes.categories.length > 0) {
      categoriesArray = attributes.categories.split(',');
    }

    console.log('cat', categoriesArray); // Function to update the number of rows.

    function changeRows(rows) {
      setAttributes({
        rows: rows
      });
    } // Function to update the number of columns.


    function changeColumns(columns) {
      setAttributes({
        columns: columns
      });
    } // Function to update the testimonial order.


    function changeOrderby(orderby) {
      setAttributes({
        orderby: orderby
      });
    } // Add a category to the active list.


    function categoriesAdd(term) {
      if (!categorySelected(term)) {
        categoriesArray.push(term.id);
      }

      setAttributes({
        categories: categoriesArray.join(',')
      });
    } // Remove a category from the active list.


    function categoriesRemove(term) {
      categoriesArray = categoriesArray.filter(function (item) {
        return parseInt(item) !== term.id;
      });
      setAttributes({
        categories: categoriesArray.join(',')
      });
    } // Is the specified category currently enabled?


    function categorySelected(term) {
      if (categoriesArray.findIndex(function (v) {
        return parseInt(v) === term.id;
      }) > -1) {
        return true;
      }

      return false;
    } // Get the list of categories as checkboxes.


    function getCategoryCheckboxes() {
      var categoryElements = [];

      if (!toolbelt_portfolio_categories) {
        return categoryElements;
      }

      Object.keys(toolbelt_portfolio_categories).forEach(function (key) {
        var term = toolbelt_portfolio_categories[key];
        categoryElements.push(createElement(CheckboxControl, {
          label: term.name,
          onChange: function onChange(state) {
            if (state) {
              categoriesAdd(term);
            } else {
              categoriesRemove(term);
            }
          },
          vale: term,
          checked: categorySelected(term)
        }));
      });
      return categoryElements;
    }

    return [createElement(ServerSideRender, {
      block: "toolbelt/portfolio",
      attributes: attributes
    }), createElement(InspectorControls, null, createElement(PanelBody, {
      title: __('Layout', 'wp-toolbelt'),
      initialOpen: true
    }, createElement(RangeControl, {
      value: attributes.rows,
      label: __('Rows', 'wp-toolbelt'),
      onChange: changeRows,
      min: 1,
      max: 10
    }), createElement(RangeControl, {
      value: attributes.columns,
      label: __('Columns', 'wp-toolbelt'),
      onChange: changeColumns,
      min: 1,
      max: 4
    }), createElement(SelectControl, {
      value: attributes.orderby,
      label: __('Order by', 'wp-toolbelt'),
      onChange: changeOrderby,
      options: [{
        value: 'date',
        label: __('date', 'wp-toolbelt')
      }, {
        value: 'rand',
        label: __('random', 'wp-toolbelt')
      }]
    })), createElement(PanelBody, {
      title: __('Project Types', 'wp-toolbelt'),
      initialOpen: true
    }, getCategoryCheckboxes()))];
  },
  save: function save() {
    return null;
  }
});