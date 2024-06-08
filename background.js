chrome.runtime.onInstalled.addListener(() => {
  console.log('Hello background');
  chrome.action.setBadgeText({
    text: "OFF",
  });
});

