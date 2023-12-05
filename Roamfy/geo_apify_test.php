<!DOCTYPE html>
<html>

<head>
  <title>Geoapify Location Autocomplete</title>
</head>

<body>
  <h1>Geoapify Location Autocomplete</h1>
  <input type="text" id="place-input" placeholder="Enter a location">
  <script>
    function initialize() {
      var input = document.getElementById('place-input');

      // Attach an event listener to the input for real-time autocomplete
      input.addEventListener('input', function () {
        var inputValue = input.value.trim();

        // Make sure the input value is not empty
        if (inputValue !== '') {
          // Fetch the actual API key from the server-side script
          fetch('get_api_key.php')
            .then(response => response.json())
            .then(data => {
              var apiKey = data.apiKey;

              // Construct the Geoapify API endpoint URL
              var apiUrl = `https://api.geoapify.com/v1/geocode/autocomplete?text=${inputValue}&apiKey=${apiKey}`;

              // Make a request to the Geoapify API
              fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                  // Handle the response data
                  console.log(data);

                  if (data.features) {
                    data.features.forEach(feature => {
                      console.log(feature.properties.name);
                    });
                  }
                })
                .catch(error => {
                  console.error('Error fetching data:', error);
                });
            })
            .catch(error => {
              console.error('Error fetching API key:', error);
            });
        }
      });
    }

    window.addEventListener('load', initialize);
  </script>
</body>

</html>
