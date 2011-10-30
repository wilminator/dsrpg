<?php require_once INCLUDE_DIR."job.php"; $GLOBALS["jobs"]=unserialize(<<<EOD
a:5:{i:1;O:3:"JOB":4:{s:4:"name";s:8:"Inventor";s:4:"need";s:3:"100";s:7:"upgrade";a:10:{s:2:"HP";d:10.98;s:2:"MP";d:4.9699999999999998;s:5:"Speed";d:8;s:8:"Accuracy";d:7;s:8:"Strength";d:8.1999999999999993;s:5:"Dodge";d:6.5;s:5:"Block";d:8;s:5:"Power";d:7;s:10:"Resistance";d:8.25;s:5:"Focus";d:5;}s:9:"abilities";a:5:{i:0;a:2:{s:7:"ability";s:1:"3";s:5:"level";s:1:"3";}i:1;a:2:{s:7:"ability";s:1:"6";s:5:"level";s:1:"4";}i:2;a:2:{s:7:"ability";s:1:"5";s:5:"level";s:1:"7";}i:3;a:2:{s:7:"ability";s:1:"4";s:5:"level";s:1:"9";}i:4;a:2:{s:7:"ability";s:1:"7";s:5:"level";s:2:"11";}}}i:2;O:3:"JOB":4:{s:4:"name";s:12:"Technomancer";s:4:"need";s:3:"100";s:7:"upgrade";a:10:{s:2:"HP";d:7.5800000000000001;s:2:"MP";d:9.8699999999999992;s:5:"Speed";d:6;s:8:"Accuracy";d:6;s:8:"Strength";d:7;s:5:"Dodge";d:6.5;s:5:"Block";d:7;s:5:"Power";d:10;s:10:"Resistance";d:9.75;s:5:"Focus";d:10.15;}s:9:"abilities";a:2:{i:0;a:2:{s:7:"ability";s:1:"1";s:5:"level";s:1:"4";}i:1;a:2:{s:7:"ability";s:1:"7";s:5:"level";s:1:"7";}}}i:3;O:3:"JOB":4:{s:4:"name";s:7:"Banshee";s:4:"need";s:3:"100";s:7:"upgrade";a:10:{s:2:"HP";d:15.56;s:2:"MP";d:6;s:5:"Speed";d:7;s:8:"Accuracy";d:5;s:8:"Strength";d:8.75;s:5:"Dodge";d:6.75;s:5:"Block";d:10;s:5:"Power";d:9.3000000000000007;s:10:"Resistance";d:10.130000000000001;s:5:"Focus";d:8.5;}s:9:"abilities";a:1:{i:0;a:2:{s:7:"ability";s:1:"2";s:5:"level";s:2:"13";}}}i:4;O:3:"JOB":4:{s:4:"name";s:6:"Marine";s:4:"need";s:3:"100";s:7:"upgrade";a:10:{s:2:"HP";d:13.57;s:2:"MP";d:0;s:5:"Speed";d:10;s:8:"Accuracy";d:8;s:8:"Strength";d:7.5;s:5:"Dodge";d:8.5;s:5:"Block";d:9;s:5:"Power";d:0;s:10:"Resistance";d:6.5;s:5:"Focus";d:0;}s:9:"abilities";a:0:{}}i:5;O:3:"JOB":4:{s:4:"name";s:7:"Bouncer";s:4:"need";s:3:"100";s:7:"upgrade";a:10:{s:2:"HP";d:19.890000000000001;s:2:"MP";d:0;s:5:"Speed";d:9;s:8:"Accuracy";d:11;s:8:"Strength";d:6.5;s:5:"Dodge";d:9;s:5:"Block";d:6;s:5:"Power";d:0;s:10:"Resistance";d:6;s:5:"Focus";d:0;}s:9:"abilities";a:0:{}}}
EOD
); $GLOBALS["jobs_js"]='[null,
  {\'name\':\'Inventor\',\'need\':100,
    upgrade:{\'HP\':10.98,\'MP\':4.97,\'Speed\':8,\'Accuracy\':7,\'Strength\':8.2,\'Dodge\':6.5,\'Block\':8,\'Power\':7,\'Resistance\':8.25,\'Focus\':5},
    abilities:[
      {\'ability\':3,\'level\':3},
      {\'ability\':6,\'level\':4},
      {\'ability\':5,\'level\':7},
      {\'ability\':4,\'level\':9},
      {\'ability\':7,\'level\':11}
    ]
  },
  {\'name\':\'Technomancer\',\'need\':100,
    upgrade:{\'HP\':7.58,\'MP\':9.87,\'Speed\':6,\'Accuracy\':6,\'Strength\':7,\'Dodge\':6.5,\'Block\':7,\'Power\':10,\'Resistance\':9.75,\'Focus\':10.15},
    abilities:[
      {\'ability\':1,\'level\':4},
      {\'ability\':7,\'level\':7}
    ]
  },
  {\'name\':\'Banshee\',\'need\':100,
    upgrade:{\'HP\':15.56,\'MP\':6,\'Speed\':7,\'Accuracy\':5,\'Strength\':8.75,\'Dodge\':6.75,\'Block\':10,\'Power\':9.3,\'Resistance\':10.13,\'Focus\':8.5},
    abilities:[
      {\'ability\':2,\'level\':13}
    ]
  },
  {\'name\':\'Marine\',\'need\':100,
    upgrade:{\'HP\':13.57,\'MP\':0,\'Speed\':10,\'Accuracy\':8,\'Strength\':7.5,\'Dodge\':8.5,\'Block\':9,\'Power\':0,\'Resistance\':6.5,\'Focus\':0},
    abilities:[]
  },
  {\'name\':\'Bouncer\',\'need\':100,
    upgrade:{\'HP\':19.89,\'MP\':0,\'Speed\':9,\'Accuracy\':11,\'Strength\':6.5,\'Dodge\':9,\'Block\':6,\'Power\':0,\'Resistance\':6,\'Focus\':0},
    abilities:[]
  }
]'; ?>