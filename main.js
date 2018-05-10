window.onload = function() {

  var table = document.getElementById("myTable").getElementsByTagName('tbody')[0];

  var json = '{ "smartphones" : [' +
    '{ "name":"Iphone" , 	"price":999, 	"display":4.5, "length":150, "width":50, "camera":5, "battery":5 },' +
    '{ "name":"Huawei" , 	"price":231, 	"display":5.5, "length":150, "width":50, "camera":1, "battery":1 },' +
    '{ "name":"MI A1" , 	"price":201, 	"display":2.5, "length":150, "width":50, "camera":2, "battery":3 },' +
    '{ "name":"LG" , 			"price":203, 	"display":5.3, "length":150, "width":50, "camera":3, "battery":4 } ]}';
  var obj;
  var listOfFilteredObjects;

  function filterJSON() {
    obj = JSON.parse(json);
    listOfFilteredObjects = [];
    for (var i = 0; i < obj.smartphones.length; i++) {

      //prize minimum
      if (document.getElementById("price_minimum_1").value != "") {
        if (obj.smartphones[i].price < document.getElementById("price_minimum_1").value) {
          continue;
        }
      }

      //prize maximum
      if (document.getElementById("price_maximum_1").value != "") {
        if (obj.smartphones[i].price > document.getElementById("price_maximum_1").value) {
          continue;
        }
      }

      //display minimum
      if (document.getElementById("size_minimum_1").value != "") {
        if (obj.smartphones[i].display < document.getElementById("size_minimum_1").value) {
          continue;
        }
      }
      //display maximum
      if (document.getElementById("size_maximum_1").value != "") {
        if (obj.smartphones[i].display > document.getElementById("size_maximum_1").value) {
          continue;
        }
      }

      //length minimum
      if (document.getElementById("size_minimum_2").value != "") {
        if (obj.smartphones[i].length < document.getElementById("size_minimum_2").value) {
          continue;
        }
      }
      //length maximum
      if (document.getElementById("size_maximum_2").value != "") {
        if (obj.smartphones[i].length > document.getElementById("size_maximum_2").value) {
          continue;
        }
      }

      //width minimum
      if (document.getElementById("size_minimum_3").value != "") {
        if (obj.smartphones[i].width < document.getElementById("size_minimum_3").value) {
          continue;
        }
      }
      //width maximum
      if (document.getElementById("size_maximum_3").value != "") {
        if (obj.smartphones[i].width > document.getElementById("size_maximum_3").value) {
          continue;
        }
      }



      //camera
      if (document.querySelector('input[name="rating-input-1"]:checked')) {
        if (obj.smartphones[i].camera < document.querySelector('input[name="rating-input-1"]:checked').value) {
          continue;
        }
      }
      //camera
      if (document.querySelector('input[name="rating-input-2"]:checked')) {
        if (obj.smartphones[i].battery < document.querySelector('input[name="rating-input-2"]:checked').value) {
          continue;
        }
      }

      listOfFilteredObjects.push(i);
    }
  }

  function sortListOfFilteredObjects() {

  }

  function deleteTable() {
    var rowCount = table.rows.length;
    for (var i = rowCount - 1; i > 0; i--) {
      table.deleteRow(i);
    }
  }

  function buildTableStructure() {
    var rowCount = table.rows.length;
    var cell1;
    var leftBoundary;
    var rightBoundary;
    for (var i = 0; i < 20; i++) {
      leftBoundary = i * 50;
      rightBoundary = (i + 1) * 50;

      if (leftBoundary < document.getElementById("price_minimum_1").value && rightBoundary > document.getElementById("price_minimum_1").value) {
        leftBoundary = document.getElementById("price_minimum_1").value;
      }
      if (leftBoundary < document.getElementById("price_maximum_1").value && rightBoundary > document.getElementById("price_maximum_1").value) {
        rightBoundary = document.getElementById("price_maximum_1").value;
      }

      if (leftBoundary < document.getElementById("price_minimum_1").value || (rightBoundary > document.getElementById("price_maximum_1").value && document.getElementById("price_maximum_1").value != 0)) {
        var row = table.insertRow();
        cell1 = row.insertCell(0);
        cell1.classList.add('padding-none');
      } else {
        var row = table.insertRow();
        cell1 = row.insertCell(0);
        cell1.className += "priceTiers";
        cell1.innerHTML = leftBoundary + "-" + rightBoundary;
      }

    }
  }

  function updateTable() {
    deleteTable();
    buildTableStructure();
    filterJSON();
    sortListOfFilteredObjects();
    for (var i = 0; i < listOfFilteredObjects.length; i++) {
      for (var e = 0; e < 20; e++) {
        if (obj.smartphones[listOfFilteredObjects[i]].price > (e) * 50 && obj.smartphones[listOfFilteredObjects[i]].price < (e + 1) * 50) {
          if (table.rows[e + 1].cells.length < 10) { //Only 10 phones per row should be shown
            var cell = table.rows[e + 1].insertCell(1);
            cell.innerHTML = "";
            cell.innerHTML += '<img class="qtip-img" height="250" src="https://images-na.ssl-images-amazon.com/images/I/81LLHSiufpL._SY879_.jpg">';
            cell.innerHTML += '<p style="text-align:center">' + obj.smartphones[listOfFilteredObjects[i]].name + '</p>'
          }

        }
      }
    }
  }


  document.getElementById("size_minimum_1").addEventListener("input", function() {
    updateTable();
  });
  var elems = document.getElementsByClassName("rating_updater");

  for (var i = 0, len = elems.length; i < len; i++) {
    elems[i].onchange = function() {
      updateTable();
    }
    elems[i].oninput = function() {
      updateTable();
    }

  }


  buildTableStructure();
  updateTable();


}