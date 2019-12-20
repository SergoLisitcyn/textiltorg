{"version":3,"sources":["script.js"],"names":["window","BX","namespace","Sender","Mail","Editor","Helper","changeDisplay","node","isShow","style","display","prototype","init","params","this","id","input","inputId","placeHolders","mess","context","containerId","blockNode","querySelector","plainNode","inputNode","addCustomEvent","onEditorInitedBefore","bind","onEditorInitedAfter","Template","Selector","selector","events","templateSelect","onTemplateSelect","template","uri","getTemplateRequestingUri","setTemplateUri","isTargetEditor","editor","indexOf","components","SetComponentIcludeMethod","extend","PlaceHolderSelectorList","BXHtmlEditor","DropDownList","Controls","onPlaceHolderSelectorListCreate","onGetControlsMap","controlsMap","push","compact","hidden","sort","checkWidth","offsetWidth","placeHolderSelectorList","isSupportedTemplateUri","value","isShowedBlock","confirmTemplateChange","BlockEditorManager","get","load","switchView","confirm","isShowBlock","setContent","content","wrap","title","placeHolderTitle","superclass","constructor","apply","arguments","action","zIndex","On","disabledForTextarea","arValues","i","CODE","name","NAME","topName","DESC","className","Create","pCont","innerHTML","appendChild","GetCont","setTimeout","Message","setAdaptedInstance"],"mappings":"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","file":""}