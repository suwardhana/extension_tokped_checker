<?php
set_time_limit(60);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getVariablesFromUrl($url)
{
  $parsed = parse_url($url);
  if (!$parsed || !isset($parsed['host']) || !isset($parsed['path'])) {
    return false;
  }

  // Extract the path component
  $path = $parsed['path'];

  $parts = explode('/', $path);
  $firstValue = $parts[1];
  $lastValue = end($parts);

  return ["toko" => $firstValue, "id_produk" => $lastValue];
}
$data_link = $_GET['data_link'] ?? [];
// $data_link = ['https://www.tokopedia.com/laku6reseller/realme-realme-c55-6gb-128gb-ex-resmi-indo-mulus-box-016f8?extParam=whid%3D388730%26src%3Dshop'];
$mh = curl_multi_init();
$handles = [];
$unique_id_target = [];
$index = 0;
foreach ($data_link as $url) {
  $extracted = getVariablesFromUrl($url);
  $handle = curl_init();
  curl_setopt_array($handle, array(
    CURLOPT_URL => 'https://gql.tokopedia.com/graphql/PDPGetLayoutQuery',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '[
    {
      "operationName": "PDPGetLayoutQuery",
      "variables": {
        "shopDomain": "' . $extracted['toko'] . '",
        "productKey": "' . $extracted['id_produk'] . '",
        "layoutID": "",
        "apiVersion": 1,
        "extParam": ""
      },
      "query": "query PDPGetLayoutQuery($shopDomain: String, $productKey: String, $layoutID: String, $apiVersion: Float, $userLocation: pdpUserLocation, $extParam: String, $tokonow: pdpTokoNow) {\\n  pdpGetLayout(\\n    shopDomain: $shopDomain\\n    productKey: $productKey\\n    layoutID: $layoutID\\n    apiVersion: $apiVersion\\n    userLocation: $userLocation\\n    extParam: $extParam\\n    tokonow: $tokonow\\n  ) {\\n    requestID\\n    name\\n    pdpSession\\n    basicInfo {\\n      alias\\n      createdAt\\n      productID\\n      isTokoNow\\n      shopID\\n      shopName\\n      defaultMediaURL\\n      minOrder\\n      maxOrder\\n      weight\\n      weightUnit\\n      condition\\n      status\\n      url\\n      sku\\n      gtin\\n      isMustInsurance\\n      needPrescription\\n      catalogID\\n      isBlacklisted\\n      isQA\\n      totalStockFmt\\n      sharingURL\\n      postATCLayout {\\n        layoutID\\n        __typename\\n      }\\n      menu {\\n        id\\n        name\\n        url\\n        __typename\\n      }\\n      category {\\n        id\\n        name\\n        title\\n        breadcrumbURL\\n        isAdult\\n        isKyc\\n        minAge\\n        detail {\\n          id\\n          name\\n          breadcrumbURL\\n          isAdult\\n          __typename\\n        }\\n        __typename\\n      }\\n      blacklistMessage {\\n        title\\n        description\\n        button\\n        url\\n        __typename\\n      }\\n      txStats {\\n        transactionSuccess\\n        transactionReject\\n        countSold\\n        paymentVerified\\n        itemSoldFmt\\n        __typename\\n      }\\n      stats {\\n        countView\\n        countReview\\n        countTalk\\n        rating\\n        __typename\\n      }\\n      shopMultilocation {\\n        isReroute\\n        cityName\\n        eduLink {\\n          webLink {\\n            action\\n            query\\n            __typename\\n          }\\n          __typename\\n        }\\n        __typename\\n      }\\n      __typename\\n    }\\n    components {\\n      name\\n      type\\n      data {\\n        ...PdpDataProductMedia\\n        ...PdpDataProductContent\\n        ...PdpDataProductInfo\\n        ...PdpDataSocialProof\\n        ...PdpDataInfo\\n        ...PdpDataUpcomingCampaign\\n        ...PdpDataCustomInfo\\n        ...PdpDataProductDetail\\n        ...PdpDataProductVariant\\n        ...PdpDataOneLiner\\n        ...PdpDataDynamicOneLiner\\n        ...PdpDataCategoryCarousel\\n        ...pdpDataCustomInfoTitle\\n        ...PdpDataOnGoingCampaign\\n        ...PdpDataProductDetailMediaComponent\\n        ...PdpDataComponentSocialProofV2\\n        ...PdpDataProductListComponent\\n        __typename\\n      }\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\\nfragment PdpDataProductVariant on pdpDataProductVariant {\\n  errorCode\\n  parentID\\n  defaultChild\\n  maxFinalPrice\\n  sizeChart\\n  componentType\\n  landingSubText\\n  postATCLayout {\\n    layoutID\\n    __typename\\n  }\\n  variants {\\n    productVariantID\\n    variantID\\n    name\\n    identifier\\n    option {\\n      picture {\\n        url\\n        url100\\n        __typename\\n      }\\n      productVariantOptionID\\n      variantUnitValueID\\n      value\\n      hex\\n      __typename\\n    }\\n    __typename\\n  }\\n  children {\\n    productID\\n    optionName\\n    price\\n    priceFmt\\n    slashPriceFmt\\n    discPercentage\\n    sku\\n    optionID\\n    subText\\n    productName\\n    productURL\\n    sharingURL\\n    picture {\\n      url\\n      url100\\n      __typename\\n    }\\n    stock {\\n      stock\\n      isBuyable\\n      stockWording\\n      stockWordingHTML\\n      minimumOrder\\n      maximumOrder\\n      stockFmt\\n      stockCopy\\n      __typename\\n    }\\n    isCOD\\n    isWishlist\\n    campaignInfo {\\n      campaignID\\n      campaignType\\n      campaignTypeName\\n      discountPercentage\\n      originalPrice\\n      discountPrice\\n      stock\\n      stockSoldPercentage\\n      threshold\\n      startDate\\n      endDate\\n      endDateUnix\\n      appLinks\\n      isAppsOnly\\n      isActive\\n      hideGimmick\\n      minOrder\\n      campaignIdentifier\\n      background\\n      paymentInfoWording\\n      __typename\\n    }\\n    thematicCampaign {\\n      additionalInfo\\n      background\\n      campaignName\\n      icon\\n      __typename\\n    }\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataProductMedia on pdpDataProductMedia {\\n  media {\\n    type\\n    suffix\\n    prefix\\n    URLThumbnail\\n    URLOriginal\\n    videoURLAndroid\\n    URLMaxRes\\n    description\\n    index\\n    variantOptionID\\n    __typename\\n  }\\n  recommendation {\\n    lightIcon\\n    darkIcon\\n    iconText\\n    bottomsheetTitle\\n    recommendation\\n    __typename\\n  }\\n  videos {\\n    source\\n    url\\n    __typename\\n  }\\n  containerType\\n  __typename\\n}\\n\\nfragment PdpDataProductContent on pdpDataProductContent {\\n  name\\n  parentName\\n  price {\\n    value\\n    currency\\n    priceFmt\\n    slashPriceFmt\\n    discPercentage\\n    __typename\\n  }\\n  campaign {\\n    campaignID\\n    campaignType\\n    campaignTypeName\\n    percentageAmount\\n    originalPrice\\n    discountedPrice\\n    originalStock\\n    stock\\n    stockSoldPercentage\\n    threshold\\n    startDate\\n    endDate\\n    endDateUnix\\n    appLinks\\n    isAppsOnly\\n    isActive\\n    hideGimmick\\n    campaignIdentifier\\n    background\\n    paymentInfoWording\\n    __typename\\n  }\\n  thematicCampaign {\\n    additionalInfo\\n    background\\n    campaignName\\n    icon\\n    __typename\\n  }\\n  stock {\\n    useStock\\n    value\\n    stockWording\\n    __typename\\n  }\\n  variant {\\n    isVariant\\n    parentID\\n    __typename\\n  }\\n  wholesale {\\n    minQty\\n    price {\\n      value\\n      currency\\n      __typename\\n    }\\n    __typename\\n  }\\n  isCashback {\\n    percentage\\n    __typename\\n  }\\n  isOS\\n  isPowerMerchant\\n  isWishlist\\n  isCOD\\n  preorder {\\n    duration\\n    timeUnit\\n    isActive\\n    preorderInDays\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataCustomInfo on pdpDataCustomInfo {\\n  icon\\n  title\\n  isApplink\\n  lightIcon\\n  applink\\n  separator\\n  description\\n  label {\\n    value\\n    color\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataProductDetail on pdpDataProductDetail {\\n  content {\\n    key\\n    type\\n    action\\n    extParam\\n    title\\n    subtitle\\n    applink\\n    showAtFront\\n    isAnnotation\\n    showAtBottomsheet\\n    icon\\n    webLink {\\n      action\\n      query\\n      __typename\\n    }\\n    __typename\\n  }\\n  title\\n  catalogBottomsheet {\\n    actionTitle\\n    param\\n    bottomSheetTitle\\n    __typename\\n  }\\n  bottomsheet {\\n    actionTitle\\n    param\\n    bottomSheetTitle\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataUpcomingCampaign on pdpDataUpcomingCampaign {\\n  campaignID\\n  campaignType\\n  campaignTypeName\\n  startDate\\n  endDate\\n  notifyMe\\n  ribbonCopy\\n  upcomingType\\n  productID\\n  descriptionHeader\\n  timerWording\\n  bgColor\\n  __typename\\n}\\n\\nfragment PdpDataCategoryCarousel on pdpDataCategoryCarousel {\\n  linkText\\n  titleCarousel\\n  applink\\n  list {\\n    categoryID\\n    icon\\n    title\\n    isApplink\\n    applink\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataProductInfo on pdpDataProductInfo {\\n  row\\n  content {\\n    title\\n    subtitle\\n    applink\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataInfo on pdpDataInfo {\\n  icon\\n  lightIcon\\n  title\\n  isApplink\\n  applink\\n  content {\\n    icon\\n    text\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataSocialProof on pdpDataSocialProof {\\n  row\\n  content {\\n    icon\\n    title\\n    subtitle\\n    applink\\n    type\\n    rating\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataOneLiner on pdpDataOneLiner {\\n  productID\\n  oneLinerContent\\n  color\\n  linkText\\n  applink\\n  separator\\n  icon\\n  isVisible\\n  eduLink {\\n    webLink {\\n      action\\n      query\\n      __typename\\n    }\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataDynamicOneLiner on pdpDataDynamicOneLiner {\\n  name\\n  text\\n  applink\\n  separator\\n  icon\\n  status\\n  chevronPos\\n  __typename\\n}\\n\\nfragment pdpDataCustomInfoTitle on pdpDataCustomInfoTitle {\\n  title\\n  status\\n  componentName\\n  __typename\\n}\\n\\nfragment PdpDataOnGoingCampaign on pdpDataOnGoingCampaign {\\n  isReleased\\n  campaign {\\n    campaignID\\n    campaignType\\n    campaignTypeName\\n    percentageAmount\\n    originalPrice\\n    discountedPrice\\n    originalStock\\n    stock\\n    stockSoldPercentage\\n    threshold\\n    startDate\\n    endDate\\n    endDateUnix\\n    appLinks\\n    isAppsOnly\\n    isActive\\n    hideGimmick\\n    campaignIdentifier\\n    background\\n    paymentInfoWording\\n    productID\\n    __typename\\n  }\\n  thematicCampaign {\\n    campaignName\\n    background\\n    icon\\n    additionalInfo\\n    productID\\n    __typename\\n  }\\n  variantCampaign {\\n    campaigns {\\n      campaignID\\n      campaignType\\n      campaignTypeName\\n      percentageAmount\\n      originalPrice\\n      discountedPrice\\n      originalStock\\n      stock\\n      stockSoldPercentage\\n      threshold\\n      startDate\\n      endDate\\n      endDateUnix\\n      appLinks\\n      isAppsOnly\\n      isActive\\n      hideGimmick\\n      campaignIdentifier\\n      background\\n      paymentInfoWording\\n      productID\\n      __typename\\n    }\\n    thematicCampaigns {\\n      campaignName\\n      background\\n      icon\\n      additionalInfo\\n      productID\\n      __typename\\n    }\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataProductDetailMediaComponent on pdpDataProductDetailMediaComponent {\\n  title\\n  description\\n  contentMedia {\\n    url\\n    ratio\\n    type\\n    __typename\\n  }\\n  show\\n  ctaText\\n  __typename\\n}\\n\\nfragment PdpDataComponentSocialProofV2 on pdpDataComponentSocialProofV2 {\\n  socialProofContent {\\n    socialProofType\\n    socialProofID\\n    title\\n    subtitle\\n    icon\\n    applink {\\n      webLink {\\n        action\\n        query\\n        __typename\\n      }\\n      __typename\\n    }\\n    URL\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment PdpDataProductListComponent on pdpDataProductListComponent {\\n  queryParam\\n  thematicID\\n  __typename\\n}"
    }
  ]',
    CURLOPT_HTTPHEADER => array(
      'X-Version: 4632b6e',
      'DNT: 1',
      'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1 Edg/119.0.0.0',
      'content-type: application/json',
      'accept: */*',
      'X-Source: tokopedia-lite',
      'x-device: mobile',
      'X-Tkpd-Lite-Service: phoenix',
      'x-tkpd-akamai: pdpGetLayout',
      'Cookie: _abck=7217B08EBF6E547817583D9272FE2C36~-1~YAAQPvrSF9B4EeaLAQAAA4FO5grzAF0Qs/U5VYnd0cQIuojHmtnJNg+OjQFX+Lui3nzz6WtSe1E0UR3rWMj/XSh2MeQ44IRmnQpYb/lnAEQSgOzFMsbFmE0s6u/KZjZhVHqRf8If0yW7yP7+JRfYCIEUZ5VVrbVOEBODkuKhD+py8STm1oiq+ex7bHNNKgidaGlGJjlevBCANf4Eeg+7mOutvBoyZ/qr5noTMtwY/9qPIUqtM7zJ9gGFIibF0+yL+N7tCPoD/t4jOr8EmZy/7L5uWOHIUCuoezdZ834YW0oKP8yyu0bBUqhbgWr3JJeLqcggcv/74+qY5Z48MqbPeo6NRTnPwGZbP5Zn9MOQ/u99AM8VNBSsOSeMrx1IqDVC~-1~-1~-1; bm_sz=4F8752BD87A57552BA373F75F69128EA~YAAQPvrSF9F4EeaLAQAAA4FO5hWubOkIVE+UZlVXsDbhM9m/MtkUWqV6DWG+3+hhdBtJCWNF3pqVkR6OwTqqHv/nHZ5Mt2/qKwqb9mJ6DW/zo15L8Dc1ITtKv2ZImhuRc1x+Ke3WhfCTErLk/hDGjGKtm08V2nJGB/11ctL3ikyVfBypXAhCwR5HkUNZPcCDBGQx6otUphufl3uhnP8VJt9u6yy9LtovnoXKvxeLEG3mI44Rl9LBvyaQ5jiwpjidPqNjhTViddSth0e0Ims03mwafxXTzaqTvgzAYZB1+qSDxQepLtY=~4339523~3424817'
    ),
  ));
  // Set other cURL options as needed

  curl_multi_add_handle($mh, $handle);
  $handles[] = $handle;
  $index++;
}

$running = null;
do {
  curl_multi_exec($mh, $running);
} while ($running > 0);

$index_to_return = [];
foreach ($handles as $i => $handle) {
  $response = curl_multi_getcontent($handle);
  if (curl_errno($handle)) {
    // Handle error
    continue;
  }

  $data = json_decode($response);
  $stok = intval($data[0]->data->pdpGetLayout->basicInfo->totalStockFmt ?? 0);
  if ($stok > 0) {
    $index_to_return[] = $i;
  }

  curl_multi_remove_handle($mh, $handle);
  curl_close($handle);
}

curl_multi_close($mh);

echo json_encode($index_to_return);
