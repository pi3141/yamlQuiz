document.addEventListener('DOMContentLoaded', () => {
    
    document.querySelector('#qcmForm').addEventListener('submit',(event)=>{
      event.preventDefault();
      event.stopPropagation();

      let target = event.target;

      var form = event.target;
      var formData = new FormData(form);

      let data = new URLSearchParams(formData);
      data.append('name', target.dataset.name);

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
    })
});

function applyCorrection(result) {
  document.querySelectorAll('.question').forEach((elem)=>{
    let questionNumber = elem.dataset.name;
    let isValid = result[questionNumber] === true;
    if (isValid) {
      elem.classList.add('green');
      elem.classList.remove('red');
    } else {
      elem.classList.add('red');
      elem.classList.remove('green');
    }
  });
  let nr_of_questions = document.querySelectorAll('fieldset.question').length;
  let nr_of_true_answer = Object.values(result).filter(Boolean).length;
  document.querySelector('#result').innerHTML = "<span id='mainScore'>Score : "+ (nr_of_true_answer / nr_of_questions * 100)+ " %</span><span id='supplScore'> (" + nr_of_true_answer+" / " + nr_of_questions+")</span>";
}