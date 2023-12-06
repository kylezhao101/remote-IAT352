<!-- code retrieved/adapted from https://www.geoapify.com/tutorial/address-input-for-address-validation-and-address-verification-forms-tutorial -->
<!-- From geoapify code samples and usages -->

<!DOCTYPE html>
<html>

<head>
  <title>Geoapify Location Autocomplete</title>
  <style>
    .autocomplete-container {
      margin-bottom: 20px;
    }

    .input-container {
      display: flex;
      position: relative;
    }

    .input-container input {
      flex: 1;
      outline: none;
      border: 1px solid rgba(0, 0, 0, 0.2);
      padding: 10px;
      padding-right: 31px;
      font-size: 16px;
    }

    .autocomplete-items {
      position: absolute;
      border: 1px solid rgba(0, 0, 0, 0.1);
      box-shadow: 0px 2px 10px 2px rgba(0, 0, 0, 0.1);
      border-top: none;
      background-color: #fff;
      z-index: 99;
      top: calc(100% + 2px);
      left: 0;
      right: 0;
    }

    .autocomplete-items div {
      padding: 10px;
      cursor: pointer;
    }

    .autocomplete-items div:hover {
      background-color: rgba(0, 0, 0, 0.1);
    }

    .autocomplete-items .autocomplete-active {
      background-color: rgba(0, 0, 0, 0.1);
    }

    .clear-button {
      color: rgba(0, 0, 0, 0.4);
      cursor: pointer;
      position: absolute;
      right: 5px;
      top: 0;
      height: 100%;
      display: none;
      align-items: center;
    }

    .clear-button.visible {
      display: flex;
    }

    .clear-button:hover {
      color: rgba(0, 0, 0, 0.6);
    }
  </style>
</head>

<body>
  <div class="autocomplete-container" id="autocomplete-container"></div>

  <script>
    function addressAutocomplete(containerElement, callback, options) {
      const MIN_ADDRESS_LENGTH = 3;
      const DEBOUNCE_DELAY = 300;

      let currentTimeout;
      let currentPromiseReject;
      let currentItems;
      let focusedItemIndex;

      const inputContainerElement = document.createElement("div");
      inputContainerElement.setAttribute("class", "input-container");
      containerElement.appendChild(inputContainerElement);

      const inputElement = document.createElement("input");
      inputElement.setAttribute("type", "text");
      inputElement.setAttribute("placeholder", options.placeholder);
      inputContainerElement.appendChild(inputElement);

      const clearButton = document.createElement("div");
      clearButton.classList.add("clear-button");
      addIcon(clearButton);
      clearButton.addEventListener("click", (e) => {
        e.stopPropagation();
        inputElement.value = '';
        callback(null);
        clearButton.classList.remove("visible");
        closeDropDownList();
      });
      inputContainerElement.appendChild(clearButton);

      inputElement.addEventListener("input", function(e) {
        const currentValue = this.value;

        if (!currentValue) {
          clearButton.classList.remove("visible");
        }

        clearButton.classList.add("visible");

        if (currentTimeout) {
          clearTimeout(currentTimeout);
        }

        if (currentPromiseReject) {
          currentPromiseReject({
            canceled: true
          });
        }

        if (!currentValue || currentValue.length < MIN_ADDRESS_LENGTH) {
          return false;
        }

        currentTimeout = setTimeout(() => {
          currentTimeout = null;

          fetch('includes/get_api_key.php')
            .then(response => response.json())
            .then(data => {
              var apiKey = data.apiKey;
              var apiUrl = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(currentValue)}&format=json&limit=5&apiKey=${apiKey}`;

              const promise = new Promise((resolve, reject) => {
                currentPromiseReject = reject;
                fetch(apiUrl)
                  .then(response => {
                    currentPromiseReject = null;
                    if (response.ok) {
                      response.json().then(data => resolve(data));
                    } else {
                      response.json().then(data => reject(data));
                    }
                  });
              });

              promise.then((data) => {
                currentItems = data.results;
                const autocompleteItemsElement = document.createElement("div");
                autocompleteItemsElement.setAttribute("class", "autocomplete-items");
                inputContainerElement.appendChild(autocompleteItemsElement);

                data.results.forEach((result, index) => {
                  const itemElement = document.createElement("div");
                  itemElement.innerHTML = result.formatted;
                  autocompleteItemsElement.appendChild(itemElement);

                  itemElement.addEventListener("click", function(e) {
                    // Set the value of the hidden input field
                    document.getElementById("selected_location").value = currentItems[index].formatted;

                    // Set the value of the visible input field
                    inputElement.value = currentItems[index].formatted;
                    callback(currentItems[index]);
                    closeDropDownList();
                  });
                });
              }, (err) => {
                if (!err.canceled) {
                  console.log(err);
                }
              });
            })
            .catch(error => {
              console.error('Error fetching API key:', error);
            });
        }, DEBOUNCE_DELAY);
      });

      document.addEventListener("click", function(e) {
        if (e.target !== inputElement) {
          closeDropDownList();
        } else if (!containerElement.querySelector(".autocomplete-items")) {
          var event = document.createEvent('Event');
          event.initEvent('input', true, true);
          inputElement.dispatchEvent(event);
        }
      });

      inputElement.addEventListener("keydown", function(e) {
        var autocompleteItemsElement = containerElement.querySelector(".autocomplete-items");
        if (autocompleteItemsElement) {
          var itemElements = autocompleteItemsElement.getElementsByTagName("div");
          if (e.keyCode == 40) {
            e.preventDefault();
            focusedItemIndex = focusedItemIndex !== itemElements.length - 1 ? focusedItemIndex + 1 : 0;
            setActive(itemElements, focusedItemIndex);
          } else if (e.keyCode == 38) {
            e.preventDefault();
            focusedItemIndex = focusedItemIndex !== 0 ? focusedItemIndex - 1 : focusedItemIndex = (itemElements.length - 1);
            setActive(itemElements, focusedItemIndex);
          } else if (e.keyCode == 13) {
            e.preventDefault();
            if (focusedItemIndex > -1) {
              closeDropDownList();
            }
          }
        } else {
          if (e.keyCode == 40) {
            var event = document.createEvent('Event');
            event.initEvent('input', true, true);
            inputElement.dispatchEvent(event);
          }
        }
      });

      function setActive(items, index) {
        if (!items || !items.length) return false;

        for (var i = 0; i < items.length; i++) {
          items[i].classList.remove("autocomplete-active");
        }

        items[index].classList.add("autocomplete-active");
        inputElement.value = currentItems[index].formatted;
        callback(currentItems[index]);
      }

      function closeDropDownList() {
        var autocompleteItemsElement = inputContainerElement.querySelector(".autocomplete-items");
        if (autocompleteItemsElement) {
          inputContainerElement.removeChild(autocompleteItemsElement);
        }
        focusedItemIndex = -1;
      }

      function addIcon(buttonElement) {
        const svgElement = document.createElementNS("http://www.w3.org/2000/svg", 'svg');
        svgElement.setAttribute('viewBox', "0 0 24 24");
        svgElement.setAttribute('height', "24");

        const iconElement = document.createElementNS("http://www.w3.org/2000/svg", 'path');
        iconElement.setAttribute("d", "M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z");
        iconElement.setAttribute('fill', 'currentColor');
        svgElement.appendChild(iconElement);
        buttonElement.appendChild(svgElement);
      }
    }

    // Initialize the addressAutocomplete
    addressAutocomplete(document.getElementById("autocomplete-container"), (data) => {
      console.log("Selected option: ");
      console.log(data);
    }, {
      placeholder: "Enter a location here"
    });
  </script>
</body>

</html>