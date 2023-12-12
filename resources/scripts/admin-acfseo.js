/* eslint-disable no-undef */
/**
 * Admin .js file
 */

document.addEventListener('DOMContentLoaded', () => {
  if (typeof acf === 'undefined' || typeof acf.Field === 'undefined') {
    return;
  }

  acf.fields.textCounter = acf.field.extend({
    type: 'text',
    events: {
      'input input': 'changeCount',
    },
    changeCount: function (e) {
      var $max = e.$el.attr('maxlength');

      if (typeof ($max) === 'undefined' || e.$el.closest('.acf-input').find('.char-count').length == 0) {
        return;
      }

      var $value = e.$el.val();
      var $length = $value.length;
      e.$el.closest('.acf-input').find('.char-count').text($length);
    },
  });

  acf.fields.textareaCounter = acf.field.extend({
    type: 'textarea',
    events: {
      'input textarea': 'changeCount',
    },
    changeCount: function (e) {
      var $max = e.$el.attr('maxlength');

      if (typeof ($max) === 'undefined') {
        return;
      }

      var $value = e.$el.val();
      var $length = $value.length;
      e.$el.closest('.acf-input').find('.char-count').text($length);
    },
  });
});
