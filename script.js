document.addEventListener('DOMContentLoaded', () => {
    
    $('#qcmForm').on('submit', (event) => {
        event.preventDefault();
        event.stopPropagation();

        let target = $(event.target);
        let data = new URLSearchParams(target.serialize());
        data.append('name', target.data('name'));

        fetch('correction.php', {
          method: 'POST',
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: data
          })
        .then(response => response.json())
        .then(result => applyCorrection(result))
        ;
      });
});

function applyCorrection(result) {
  $('.question').each((index, elem) => {
    let $elem = $(elem);
    let questionNumber = $elem.data('name');
    let isValid = result[questionNumber] === true;
    if (isValid) {
      $elem.addClass('green');
      $elem.removeClass('red');
    } else {
      $elem.addClass('red');
      $elem.removeClass('green');
    }
  });
}