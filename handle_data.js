document.getElementById("save_link").addEventListener("click", saveUrl);
document.getElementById("clear_all_link").addEventListener("click", clearAllLink);

// showAllLink(); // uncomment this line to show all link from storage
document.addEventListener("DOMContentLoaded", fetchFirst);

function saveUrl() {
  chrome.tabs.query({ active: true, currentWindow: true }, function (tabs) {
    console.log(tabs);
    var currentUrl = tabs[0].url;
    var currentTitle = getCleanTitle(tabs[0].title);
    var urlObject = {
      url: currentUrl,
      title: currentTitle
    };
    // check if current url contains tokopedia.com
    if (currentUrl.includes("tokopedia.com") == false) {
      return;
    }
    // save url to local storage
    var urlList = JSON.parse(localStorage.getItem("urlList")) || [];
    urlList.push(urlObject);
    localStorage.setItem("urlList", JSON.stringify(urlList));
    // get local storage item url list
    var allUrls = JSON.parse(localStorage.getItem("urlList"));
    console.log(allUrls);
    alert("URL saved");
    fetchFirst();
  });
}

function clearAllLink() {
  localStorage.clear();
}

/* function showAllLink() {
  // clear content of #show_link_here, and then show all link from storage as ul li
  var showLinkHere = document.getElementById("list_available_link");
  showLinkHere.innerHTML = "";
  var allUrls = JSON.parse(localStorage.getItem("urlList"));
  if (allUrls.length > 0) {
    for (var i = 0; i < allUrls.length; i++) {
      var link = document.createElement("div");
      link.className = "list-group-item";
      link.dataset.linkUrl = allUrls[i].url;
      link.dataset.linkTitle = allUrls[i].title;
      link.innerHTML = `<div class="d-flex justify-content-between align-items-center">
        <div class="title-link">
          ${allUrls[i].title}
        </div>
        <div>
          <i class="fa fa-remove fa-fw"></i>
        </div>
      </div>`;

      var titleLink = link.querySelector(".title-link");
      titleLink.addEventListener("click", function (event) {
        event.preventDefault();
        var url_new_tab = this.parentNode.parentNode.dataset.linkUrl;
        chrome.tabs.create({ url: url_new_tab });
      });
      var removeIcon = link.querySelector(".fa-remove");
      removeIcon.addEventListener("click", function (event) {
        var index = event.target.dataset.index;
        removeItemFromStoragebyIndex(index);
      });
      showLinkHere.appendChild(link);
    };
  }
} */

function getCleanTitle(title) {
  const inputString = title
  const regex = /Promo (.*?) Cicil/;
  const match = inputString.match(regex);

  if (match) {
    return match[1];
  } else {
    return title;
  }
}

function removeItemFromStoragebyIndex(index) {
  console.log(index);
  alert('deleted index' + index);
  var allUrls = JSON.parse(localStorage.getItem("urlList"));
  allUrls.splice(index, 1);
  localStorage.setItem("urlList", JSON.stringify(allUrls));
  fetchFirst();
}

function fetchFirst() {
  // ajax to https://imtheluckiest.com/shopee/tokped/get_to_post.php GET with param data_link[] from urlList
  var urlList = JSON.parse(localStorage.getItem("urlList"));
  if (urlList == null) return;
  var dataLinkArray = urlList.map(function (urlObject) {
    return urlObject.url;
  });
  var showLinkHere = document.getElementById("list_available_link");
  var showNotAvailableLinkHere = document.getElementById("list_not_available_link");
  showLinkHere.innerHTML = "";
  showNotAvailableLinkHere.innerHTML = "";
  $.ajax({
    url: "https://imtheluckiest.com/shopee/tokped/get_to_post.php",
    type: "GET",
    data: $.param({ data_link: dataLinkArray }),
    traditional: true,
    success: function (response) {
      // return index that available ex = [0,5,10]
      // decode json
      var indexToShow = JSON.parse(response);
      // show urlList with index from indexToShow
      indexToShow.forEach(index => {
        var link = document.createElement("div");
        link.className = "list-group-item";
        link.style = "cursor : pointer";
        link.dataset.linkUrl = urlList[index].url;
        link.dataset.linkTitle = urlList[index].title;
        link.innerHTML = `<div class="d-flex justify-content-between align-items-center">
        <div class="title-link">
          ${urlList[index].title}
        </div>
        <div>
          <i class="fa fa-remove fa-fw"></i>
        </div>
      </div>`;

        var titleLink = link.querySelector(".title-link");
        titleLink.addEventListener("click", function (event) {
          event.preventDefault();
          var url_new_tab = this.parentNode.parentNode.dataset.linkUrl;
          chrome.tabs.create({ url: url_new_tab });
        });
        var removeIcon = link.querySelector(".fa-remove");
        removeIcon.addEventListener("click", function (event) {
          removeItemFromStoragebyIndex(index);
        });
        showLinkHere.appendChild(link);
      });
      // show to showNotAvailableLinkHere for index that is not on indextoshow
      for (var i = 0; i < urlList.length; i++) {
        if (!indexToShow.includes(i)) {
          var link = document.createElement("div");
          link.className = "list-group-item";
          link.dataset.linkUrl = urlList[i].url;
          link.dataset.linkTitle = urlList[i].title;
          link.innerHTML = `<div class="d-flex justify-content-between align-items-center">
          <div class="title-link">
            ${urlList[i].title} ${i}
          </div>
          <div>
            <i class="fa fa-remove fa-fw"></i>
          </div>
        </div>`;
          var removeIcon = link.querySelector(".fa-remove");
          removeIcon.addEventListener("click", (function (index) {
            return function (event) {
              console.log("event listener" + index);
              removeItemFromStoragebyIndex(index);
            };
          })(i));
          showNotAvailableLinkHere.appendChild(link);
        }
      }
    }
  });
}


