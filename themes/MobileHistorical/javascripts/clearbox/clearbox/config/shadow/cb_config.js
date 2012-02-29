/*
// 	ClearBox Config File (JavaScript)
*/

var

// CB layout:

	CB_WindowColor='#fff',				// color of the CB window (note: you have to change the rounded-corner PNGs too!), transparent is also working
	CB_MinWidth=0,					// minimum (only at images) or initial width of CB window
	CB_MinHeight=0,					// initial heigth of CB window
	CB_WinPadd=15,					// padding of the CB window from sides of the browser 
	CB_RoundPix=13,					// change this setting only if you are generating new PNGs for rounded corners
	CB_ImgBorder=0,					// border size around the picture in CB window
	CB_ImgBorderColor='#ddd',			// border color around the picture in CB window
	CB_Padd=0,					// padding around inside the CB window

	CB_BodyMarginLeft=0,				//
	CB_BodyMarginRight=0,				// if you set margin to <body>,
	CB_BodyMarginTop=0,				// please change these settings!
	CB_BodyMarginBottom=0,				//

	CB_ShowThumbnails='auto',			// it tells CB how to show the thumbnails ('auto', 'always' or 'off') thumbnails are only in picture-mode!
	CB_ThumbsBGColor='#000',			// color of the thumbnail layer
	CB_ThumbsBGOpacity=.35,				// opacity of the thumbnail layer
	CB_ActThumbOpacity=.65,				// thumbnail opacity of the current viewed image

	CB_SlideShowBarColor='#000',			// color of the slideshow bar
	CB_SlideShowBarOpacity=.5,			// opacity of the slideshow bar
	CB_SlideShowBarPadd=0,				// padding of the slideshow bar (left and right)
	CB_SlideShowBarTop=-5,				// position of the slideshow bar from the top of the picture

	CB_SimpleDesign='on',				// if it's 'on', CB doesn't show the frame but only the content - really nice ;)

	CB_CloseBtnTop=1,				// vertical position of the close button in picture mode
	CB_CloseBtnRight=0,				// horizontal position of the close button in picture mode
	CB_CloseBtn2Top=-20,				// vertical position of the close button in content mode
	CB_CloseBtn2Right=-30,				// horizontal position of the close button in content mode

	CB_OSD='on',					// turns on OSD
	CB_OSDShowReady='on',				// when clearbox is loaded and ready, it shows an OSD message

// CB font, text and navigation (at the bottom of CB window) settings:

	CB_FontT='helvetica, arial, sans-serif',				//
	CB_FontSizeT=12,				// these variables are referring to the title or caption line
	CB_FontColorT='#777',				// 
	CB_FontWeightT='normal',			//

	CB_FontC='helvetica, arial, sans-serif',				//
	CB_FontSizeC=11,				// these variables are referring to
	CB_FontColorC='#aaa',				// comment lines under the title
	CB_FontWeightC='normal',			//
	CB_TextAlignC='justify',			//
      	CB_txtHCMax=120,				// maximum height of the comment box in pixels

	CB_FontG='helvetica, arial, sans-serif',				//
	CB_FontSizeG=11,				// these variables are referring to the gallery name
	CB_FontColorG='#999',				//
	CB_FontWeightG='normal',			//

	CB_PadT=10,					// space in pixels between the content and the title or caption line

	CB_OuterNavigation='on',			// turns outer navigation panel on

	CB_ShowURL='off',				// it shows the url of the content if no title or caption is given
	CB_ItemNum='on',				// it shows the ordinal number of the content in galleries
	CB_ItemNumBracket='()',				// whatever you want ;)

	CB_ShowGalName='on',				// it shows gallery name
	CB_TextNav='on',				// it shows previous and next navigation
	CB_NavTextImgPrvNxt='on',			// it shows previous and next buttons instead of text
	CB_ShowDL='on',					// it shows download controls
	CB_NavTextImgDL='on',				// it shows download buttons instead of text

	CB_ImgRotation='on',				// it shows the image rotation controls
	CB_NavTextImgRot='on',				// it shows the image rotation buttons instead of text

// Settings of the document-hiding layer:

	CB_HideColor='#fff',				// color of the layer
	CB_HideOpacity=.95,				// opacity (0 is invisible, 1 is 100% color) of the layer
	CB_HideOpacitySpeed=400,			// speed of fading
	CB_CloseOnH='on',				// CB will close, if you click on background

// CB animation settings:

	CB_Animation='growinout',			// 'double', 'normal', 'off', 'grow', 'growinout' or 'warp' (high cpu usage)
	CB_ImgOpacitySpeed=300,				// speed of content fading (in ms)
	CB_TextOpacitySpeed=10,				// speed of text fading under the picture (in ms)
	CB_AnimSpeed=500,				// speed of the resizing animation of CB window (in ms)
	CB_ImgTextFade='on',				// turns on or off the fading of content and text
	CB_FlashHide='off',				// it hides flash animations on the page before CB starts
	CB_SelectsHide='on',				// it hides select boxes on the page before CB starts
	CB_SlShowTime=5,				// default speed of the slideshow (in sec)
	CB_Preload='on',				// preload neighbour pictures in galleries
	CB_ShowLoading='off',				// NOT WORKING IN THIS VERSION (3.5beta)

// Images using by CB settings:

	CB_PictureStart='start.png',			//
	CB_PicturePause='pause.png',			//
	CB_PictureClose='close.png',			// filenames of some images using by clearbox
	CB_PictureNext='next.png',			// (there are other images specified in clearbox.css!)
	CB_PicturePrev='prev.png',			//

// CB professional settings:

	CB_PicDir=CB_ScriptDir+'/config/'+CB_Config+'/pic',	// CHANGE ONLY IF YOU CHANGED THE DEFAULT DIRECTORY-STRUCTURE OF CB!

	CB_AllowedToRun='on',					// if 'off', CB won't start (you can change this variable without reload!)
	CB_AllowExtFunctLoad='off',				// if 'on', CB will run a function named CB_ExternalFunctionLoad(); every time after a new content has loaded (useful for audit, etc)
	CB_AllowExtFunctPageLoad='off',				// if 'on', CB will run a function named CB_ExternalFunctionPageLoad(); after your page has fully loaded
	CB_AllowExtFunctCBClose='off'				// if 'on', CB will run a function named CB_ExternalFunctionCBClose(); after CB window has closed

;