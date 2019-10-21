gama3.form.MultipleFileUpload = Ext.extend(Ext.form.Field, {

    defaultAutoCreate : {tag: 'input', type: 'hidden', size: '20', autocomplete: 'off'},

    uploadUrl: null,
    deleteUrl: null,
    viewUrl: null,
    uploadFileTypes: "*.jpg;*.png;*.gif;*.pdf;*.doc;*.docx;*.ppt;*.pptx;*.txt;*.xls;*.xlsx;",
    uploadFileTypesDescription: "Documentos",
    width: 500,
    height: 190,
    gridWidth: 500,
    gridHeight: 140,

    initComponent: function(){
        gama3.form.MultipleFileUpload.superclass.initComponent.apply(this, arguments);

        this.initAwesomeUploader();
    }

    /**
    * @override
    */
    ,onRender: function(){
            gama3.form.MultipleFileUpload.superclass.onRender.apply(this, arguments);

            this.wrap = this.el.wrap();
            this.awesomeUploader.render(this.wrap);
    }

    ,initAwesomeUploader: function(){
        this.awesomeUploader = new gama3.form.AwesomeUploader({
                width: this.gridWidth+40
                ,height: this.gridHeight+50
                ,gridHeight: this.gridHeight
                ,gridWidth: this.gridWidth
                ,allowBlank: this.allowBlank
                ,flashSwfUploadFileTypes: this.uploadFileTypes
                ,flashSwfUploadFileTypesDescription: this.uploadFileTypesDescription
                ,awesomeUploaderRoot: './lib/gama/interface_web/ext/ux/awesome_uploader/'
                ,flashUploadUrl: this.uploadUrl
                ,deleteUrl: this.deleteUrl
                ,viewUrl: this.viewUrl
                ,listeners:{
                        scope:this
                        ,fileadded:function()
                        {
                                this.fireEvent("fileadded");
                        }.createDelegate(this)
                        ,fileupload: this.fileUploadHandler.createDelegate(this)
                        ,allfilesupload:function(){
                                this.fireEvent("allfilesupload");
                        }.createDelegate(this)
                        ,filedelete: this.filedelete.createDelegate(this)
                }
        });
    }

    ,fileUploadHandler: function(uploader, success, result, fileRec){
        if(success)
        {
                if(result.data != undefined)
                {
                        this.awesomeUploader.updateFile(fileRec, 'id', result.data.id, true);
                        this.awesomeUploader.updateFile(fileRec, 'name', result.data.name, true);
                        this.awesomeUploader.updateFile(fileRec, 'path', result.data.path, true);
                        this.setValueInField();
                }
        }
    }

    ,filedelete:function(success, record){
        if(success)
        {
                this.setValueInField();
        }
    }

    /**
    * @interface {gama3.form.InterfaceFieldFormAlt}
    * @public
    */
    ,showFormAltLoad: function(data){
        this.awesomeUploader.loadRecords(data);
        this.setValueInField();
    }

    /**
    * Coloca o valor dos ids selecionados a partir da função serializeFuncion informada pelo usuário
    * Ex: "1;9;234;13"
    * @private
    */
    ,setValueInField: function(){
        this.getEl().dom.value = this.awesomeUploader.serializeFunction();
    }

});

/**
 * Cópia de Ext.ux.AwesomeUploader porém foram feitos alguns ajutes para ser utilizado no MultipleFileUpload.
 */
gama3.form.AwesomeUploader = Ext.extend(Ext.Panel, {
	jsonUrl:'/test/router/fileMan/'
	,jsonUrlUpload:'upload'
        ,viewUrl: ''
	,swfUploadItems:[]
	,doLayout:function(){
		gama3.form.AwesomeUploader.superclass.doLayout.apply(this, arguments);
		this.fileGrid.getView().refresh();
	}
	,initComponent:function(){

		this.addEvents(
			'fileupload'
				// fireEvent('fileupload', Obj thisUploader, Bool uploadSuccessful, Obj serverResponse);
				//server response object will at minimum have a property "error" describing the error.
			,'fileselectionerror'
				// fireEvent('fileselectionerror', String message)
				//fired by drag and drop and swfuploader if a file that is too large is selected.
				//Swfupload also fires this even if a 0-byte file is selected or the file type does not match the "flashSwfUploadFileTypes" mask
			,'filedelete'
			,'allfilesupload'
			,'fileadded'
		);

		var fields = ['id', 'name', 'path', 'size', 'status', 'progress', 'delete'];
		this.fileRecord = Ext.data.Record.create(fields);

		this.initialConfig = this.initialConfig || {};
		this.initialConfig.awesomeUploaderRoot = this.initialConfig.awesomeUploaderRoot || '';

		Ext.apply(this, this.initialConfig, {
			flashButtonSprite:this.initialConfig.awesomeUploaderRoot+'swfupload_browse_button_trans_56x22.PNG'
			,flashButtonWidth:'56'
			,flashButtonHeight:'22'
			,flashUploadFilePostName:'Filedata'
			,disableFlash:false
			,flashSwfUploadPath:this.initialConfig.awesomeUploaderRoot+'swfupload.swf'
			//,flashSwfUploadFileSizeLimit:'3 MB' //deprecated
			,flashSwfUploadFileTypes:'*.pdf;*.doc;*.docx;*.ppt;*.pptx;*.txt;*.xls;*.xlsx;'
			,flashSwfUploadFileTypesDescription:'Documentos'
			,deleteUrl:this.initialConfig.awesomeUploaderRoot+'delete.php'
			,flashUploadUrl:this.initialConfig.awesomeUploaderRoot+'upload.php'
			,xhrUploadUrl:this.initialConfig.awesomeUploaderRoot+'xhrupload.php'
			,xhrFileNameHeader:'X-File-Name'
			,xhrExtraPostDataPrefix:'extraPostData_'
			,xhrFilePostName:'Filedata'
			,xhrSendMultiPartFormData:false
//			,maxFileSizeBytes: 3145728 // 3 * 1024 * 1024 = 3 MiB
			,maxFileSizeBytes: 2147483647 // 2GB
			,standardUploadFilePostName:'Filedata'
			,standardUploadUrl:this.initialConfig.awesomeUploaderRoot+'upload.php'
			,iconStatusPending:this.initialConfig.awesomeUploaderRoot+'hourglass.png'
			,iconStatusSending:this.initialConfig.awesomeUploaderRoot+'loading.gif'
			,iconStatusAborted:this.initialConfig.awesomeUploaderRoot+'cross.png'
			,iconStatusError:this.initialConfig.awesomeUploaderRoot+'cross.png'
			,iconStatusDone:this.initialConfig.awesomeUploaderRoot+'tick.png'
			,iconDelete:this.initialConfig.awesomeUploaderRoot+'delete.png'
			,supressPopups:false
			,extraPostData:{}
			,width:440
			,height:250
			,autoScroll: true
			,border:true
			,frame:true
			,layout:'absolute'
			,fileId:0
			,addedFiles:0
			,processedFiles:0
			,items:[
			{
				//swfupload and upload button container
			},{
				xtype:'grid'
				,x:0
				,y:30
				,width:this.initialConfig.gridWidth || 420
				,height:this.initialConfig.gridHeight || 200
				,enableHdMenu:false
				,store:new Ext.data.ArrayStore({
					fields: fields
					,reader: new Ext.data.ArrayReader({idIndex: 0}, this.fileRecord)
				})
				,columns:[
					{header: "", width: 30, dataIndex:"id", renderer: function(val) {return "<a href='#show:"+val+"' ><img src='./lib/gama/interface_web/gama3/resources/img/icones/visualizar.png' title='Visualizar' /></a>"}}
                                        ,{header:'Arquivo',dataIndex:'name', width:170}
					,{header:'Tamanho',dataIndex:'size', width:60, renderer:Ext.util.Format.fileSize}
					,{header:'&nbsp;',dataIndex:'status', width:30, scope:this, renderer:this.statusIconRenderer}
					,{header:'Status',dataIndex:'status', width:60}
					,{header:'Progresso',dataIndex:'progress',scope:this, renderer:this.progressBarColumnRenderer}
					,{header:'Excluir',dataIndex:'iconDelete', width:45}
				]
				,listeners:{
					render:{
						scope:this
						,fn:function(){
							this.fileGrid = this.items.items[1];
							this.initFlashUploader();
							this.initDnDUploader();
						}
					},
					cellclick: function(val, rowIndex, columnIndex, event){
						if(columnIndex == 0){
                                                    var store = this.fileGrid.getStore();
                                                    this.viewFile(store.getAt(rowIndex),rowIndex);
                                                }else if(columnIndex == 6){
                                                    var store = this.fileGrid.getStore();
                                                    this.deleteFile(store.getAt(rowIndex),rowIndex);
                                                }
                     }.createDelegate(this)
				}
			}]
		});

		gama3.form.AwesomeUploader.superclass.initComponent.apply(this, arguments);
	}

        ,viewFile: function(record, rowIndex){
            window.open(this.viewUrl+"&path="+record.get("path"),"_blank");
        }

	,fileAlert:function(text){
		if(this.supressPopups){
			return true;
		}
		if(this.fileAlertMsg === undefined || !this.fileAlertMsg.isVisible()){
			this.fileAlertMsgText = 'Error uploading:<BR>'+text;
			this.fileAlertMsg = Ext.MessageBox.show({
				title:'Upload Error',
				msg: this.fileAlertMsgText,
				buttons: Ext.Msg.OK,
				modal:false,
				icon: Ext.MessageBox.ERROR
			});
		}else{
				this.fileAlertMsgText += text;
				this.fileAlertMsg.updateText(this.fileAlertMsgText);
				this.fileAlertMsg.getDialog().focus();
		}

	}
	,statusIconRenderer:function(value){

		switch(value){
			default:
				return value;
			case 'Pendente':
				return '<img src="'+this.iconStatusPending+'" width=16 height=16>';
			case 'Enviando':
				return '<img src="'+this.iconStatusSending+'" width=16 height=16>';
			case 'Abortado':
				return '<img src="'+this.iconStatusAborted+'" width=16 height=16>';
			case 'Erro':
				return '<img src="'+this.iconStatusError+'" width=16 height=16>';
			case 'Completo':
				return '<img src="'+this.iconStatusDone+'" width=16 height=16>';
		}
	}
	,progressBarColumnTemplate: new Ext.XTemplate(
			'<div class="ux-progress-cell-inner ux-progress-cell-inner-center ux-progress-cell-foreground">',
				'<div>{value} %</div>',
			'</div>',
			'<div class="ux-progress-cell-inner ux-progress-cell-inner-center ux-progress-cell-background" style="left:{value}%">',
				'<div style="left:-{value}%">{value} %</div>',
			'</div>'
    )
	,progressBarColumnRenderer:function(value, meta, record, rowIndex, colIndex, store){
        meta.css += ' x-grid3-td-progress-cell';
		return this.progressBarColumnTemplate.apply({
			value: value
		});
	}
	,addFile:function(file){

		this.addedFiles++;
		this.fireEvent('fileadded');

		var fileRec = new this.fileRecord(
			Ext.apply(file,{
				id: this.fileId++
				,status: 'Pendente'
				,progress: '0'
				,complete: '0'
			})
		);
		this.fileGrid.store.add(fileRec);

		return fileRec;
	}
	,addCompleteFile:function(file){
		var fileRec = new this.fileRecord(
				Ext.apply(file,{
					id: file.id
					,status: 'Completo'
					,progress: '100'
					,complete: file.size
					,iconDelete: '<img src="'+this.iconDelete+'" width="16" height="16" style="margin-left:8px;cursor:pointer;">'
				})
		);
		this.fileGrid.store.add(fileRec);

		return fileRec;
	}
	,deleteFile:function(record,rowIndex){
		Ext.Ajax.request({
		   url: this.deleteUrl,
		   success: function(success){
			   if(success)
			   {
				   this.fileGrid.getStore().removeAt(rowIndex);
				   this.fireEvent('filedelete', true, record);
			   }
			   else
				   this.fireEvent('filedelete', false);

		   }.createDelegate(this),
		   failure: function(){
			   this.fireEvent('filedelete', false);
		   }.createDelegate(this),
		   params: {
                       id: record.get("id")
                       ,name: record.get("name")
                       ,path: record.get("path")
                   }
		});
	}
	,updateFile:function(fileRec, key, value, completed){

		fileRec.set(key, value);

		completed = completed != undefined ? completed : false;

		if(completed)
			fileRec.set('iconDelete', '<img src="'+this.iconDelete+'" width="16" height="16" style="margin-left:8px;cursor:pointer;">');

		fileRec.commit();
	}
	,initStdUpload:function(param){
		if(this.uploader){
			this.uploader.fileInput = null; //remove reference to file field. necessary to prevent destroying file field during upload.
			Ext.destroy(this.uploader);
		}else{
			Ext.destroy(this.items.items[0]);
		}
		this.uploader = new Ext.ux.form.FileUploadField({
			renderTo:this.body
			,buttonText:'Browse...'
			,x:0
			,y:0
			,style:'position:absolute;'
			,buttonOnly:true
			,name:this.standardUploadFilePostName
			,listeners:{
				scope:this
				,fileselected:this.stdUploadFileSelected
			}
		});

	}
	,initFlashUploader:function(){

		if(this.disableFlash){
			this.initStdUpload();
			return true;
		}

		var settings = {
			flash_url: this.flashSwfUploadPath
			,upload_url: this.flashUploadUrl
			,file_size_limit: this.maxFileSizeBytes + ' B'
			,file_types: this.flashSwfUploadFileTypes
			,file_types_description: this.flashSwfUploadFileTypesDescription
			,file_upload_limit: 100
			,file_queue_limit: 0
			,debug: false
			,post_params: this.extraPostData
			,button_image_url: this.flashButtonSprite
			,button_width: this.flashButtonWidth
			,button_height: this.flashButtonHeight
			,button_window_mode: 'opaque'
			,file_post_name: this.flashUploadFilePostName
			,button_placeholder: this.items.items[0].body.dom
			,file_queued_handler: this.swfUploadfileQueued.createDelegate(this)
			,file_dialog_complete_handler: this.swfUploadFileDialogComplete.createDelegate(this)
			,upload_start_handler: this.swfUploadUploadStart.createDelegate(this)
			,upload_error_handler: this.swfUploadUploadError.createDelegate(this)
			,upload_progress_handler: this.swfUploadUploadProgress.createDelegate(this)
			,upload_success_handler: this.swfUploadSuccess.createDelegate(this)
			,upload_complete_handler: this.swfUploadComplete.createDelegate(this)
			,file_queue_error_handler: this.swfUploadFileQueError.createDelegate(this)
			,minimum_flash_version: '9.0.28'
			,swfupload_load_failed_handler: this.initStdUpload.createDelegate(this)
		};
		this.swfUploader = new SWFUpload(settings);
	}
	,initDnDUploader:function(){

		//==================
		// Attach drag and drop listeners to document body
		// this prevents incorrect drops, reloading the page with the dropped item
		// This may or may not be helpful
		if(!document.body.BodyDragSinker){
			document.body.BodyDragSinker = true;

			var body = Ext.fly(document.body);
			body.on({
				dragenter:function(event){
					return true;
				}
				,dragleave:function(event){
					return true;
				}
				,dragover:function(event){
					event.stopEvent();
					return true;
				}
				,drop:function(event){
					event.stopEvent();
					return true;
				}
			});
		}
		// end body events
		//==================

		this.el.on({
			dragenter:function(event){
				event.browserEvent.dataTransfer.dropEffect = 'move';
				return true;
			}
			,dragover:function(event){
				event.browserEvent.dataTransfer.dropEffect = 'move';
				event.stopEvent();
				return true;
			}
			,drop:{
				scope:this
				,fn:function(event){
					event.stopEvent();
					var files = event.browserEvent.dataTransfer.files;

					if(files === undefined){
						return true;
					}
					var len = files.length;
					while(--len >= 0){
						this.processDnDFileUpload(files[len]);
					}
				}
			}
		});

	}
	,processDnDFileUpload:function(file){

		var fileRec = this.addFile({
			name: file.name
			,size: file.size
		});

		if(file.size > this.maxFileSizeBytes){
			this.updateFile(fileRec, 'status', 'Erro');
			this.fileAlert('<BR>'+file.name+'<BR><b>File size exceeds allowed limit.</b><BR>');
			this.fireEvent('fileselectionerror', 'File size exceeds allowed limit.');
			return true;
		}

		var upload = new Ext.ux.XHRUpload({
			url:this.xhrUploadUrl
			,filePostName:this.xhrFilePostName
			,fileNameHeader:this.xhrFileNameHeader
			,extraPostData:this.extraPostData
			,sendMultiPartFormData:this.xhrSendMultiPartFormData
			,file:file
			,listeners:{
				scope:this
				,uploadloadstart:function(event){
					this.updateFile(fileRec, 'status', 'Enviando');
				}
				,uploadprogress:function(event){
					this.updateFile(fileRec, 'progress', Math.round((event.loaded / event.total)*100));
				}
				// XHR Events
				,loadstart:function(event){
					this.updateFile(fileRec, 'status', 'Enviando');
				}
				,progress:function(event){
					fileRec.set('progress', Math.round((event.loaded / event.total)*100) );
					fileRec.commit();
				}
				,abort:function(event){
					this.updateFile(fileRec, 'status', 'Abortado');
					this.processedFiles++;

					this.fireEvent('fileupload', this, false, {error:'XHR upload aborted'});

					if(this.addedFiles == this.processedFiles)
						this.fireEvent('allfilesupload');
				}
				,error:function(event){
					this.updateFile(fileRec, 'status', 'Erro');
					this.processedFiles++;

					this.fireEvent('fileupload', this, false, {error:'XHR upload error'});

					if(this.addedFiles == this.processedFiles)
						this.fireEvent('allfilesupload');
				}
				,load:function(event){

					try{
						var result = Ext.util.JSON.decode(upload.xhr.responseText);//throws a SyntaxError.
					}catch(e){
						Ext.MessageBox.show({
							buttons: Ext.MessageBox.OK
							,icon: Ext.MessageBox.ERROR
							,modal:false
							,title:'Upload Error!'
							,msg:'Invalid JSON Data Returned!<BR><BR>Please refresh the page to try again.'
						});
						this.updateFile(fileRec, 'status', 'Erro');
						this.processedFiles++;

						this.fireEvent('fileupload', this, false, {error:'Invalid JSON returned'});

						if(this.addedFiles == this.processedFiles)
							this.fireEvent('allfilesupload');

						return true;
					}
					if( result.success ){
						fileRec.set('progress', 100 );
						fileRec.set('status', 'Completo');
						fileRec.commit();
						this.processedFiles++;
						this.fireEvent('fileupload', this, true, result, fileRec);

						if(this.addedFiles == this.processedFiles)
							this.fireEvent('allfilesupload');

					}else{
						this.fileAlert('<BR>'+file.name+'<BR><b>'+result.error+'</b><BR>');
						this.updateFile(fileRec, 'status', 'Erro');
						this.processedFiles++;
						this.fireEvent('fileupload', this, false, result);

						if(this.addedFiles == this.processedFiles)
							this.fireEvent('allfilesupload');
					}
				}
			}
		});
		upload.send();
	}
	,swfUploadUploadProgress:function(file, bytesComplete, bytesTotal){
		this.updateFile(this.swfUploadItems[file.index], 'progress', Math.round((bytesComplete / bytesTotal)*100));
	}
	,swfUploadFileDialogComplete:function(){
		this.swfUploader.startUpload();
	}
	,swfUploadUploadStart:function(file){
		this.swfUploader.setPostParams(this.extraPostData); //sync post data with flash
		this.updateFile(this.swfUploadItems[file.index], 'status', 'Enviando');
	}
	,swfUploadComplete:function(file){ //called if the file is errored out or on success
		this.swfUploader.startUpload(); //as per the swfupload docs, start the next upload!
	}
	,swfUploadUploadError:function(file, errorCode, message){
		this.fileAlert('<BR>'+file.name+'<BR><b>'+message+'</b><BR>');//SWFUpload.UPLOAD_ERROR_DESC[errorCode.toString()]

		this.updateFile(this.swfUploadItems[file.index], 'status', 'Erro');
		this.processedFiles++;
		this.fireEvent('fileupload', this, false, {error:message});

		if(this.addedFiles == this.processedFiles)
			this.fireEvent('allfilesupload');
	}
	,swfUploadSuccess:function(file, serverData){ //called when the file is done
		try{
			var result = Ext.util.JSON.decode(serverData);//throws a SyntaxError.
		}catch(e){
			Ext.MessageBox.show({
				buttons: Ext.MessageBox.OK
				,icon: Ext.MessageBox.ERROR
				,modal:false
				,title:'Upload Error!'
				,msg:'Invalid JSON Data Returned!<BR><BR>Please refresh the page to try again.'
			});
			this.updateFile(this.swfUploadItems[file.index], 'status', 'Erro');
			this.processedFiles++;
			this.fireEvent('fileupload', this, false, {error:'Invalid JSON returned'});

			if(this.addedFiles == this.processedFiles)
				this.fireEvent('allfilesupload');

			return true;
		}
		if( result.success ){
			this.swfUploadItems[file.index].set('progress',100);
			this.swfUploadItems[file.index].set('status', 'Completo');
			this.swfUploadItems[file.index].commit();
			this.processedFiles++;
			this.fireEvent('fileupload', this, true, result, this.swfUploadItems[file.index]);

			if(this.addedFiles == this.processedFiles)
				this.fireEvent('allfilesupload');

		}else{
			this.fileAlert('<BR>'+file.name+'<BR><b>'+result.error+'</b><BR>');
			this.updateFile(this.swfUploadItems[file.index], 'status', 'Erro');
			this.processedFiles++;
			this.fireEvent('fileupload', this, false, result);

			if(this.addedFiles == this.processedFiles)
				this.fireEvent('allfilesupload');
		}
	}
	,swfUploadfileQueued:function(file){
		this.swfUploadItems[file.index] = this.addFile({
			name: file.name
			,size: file.size
		});
		return true;
	}
	,swfUploadFileQueError:function(file, error, message){
		this.swfUploadItems[file.index] = this.addFile({
			name: file.name
			,size: file.size
		});
		this.updateFile(this.swfUploadItems[file.index], 'status', 'Erro');
		this.fileAlert('<BR>'+file.name+'<BR><b>'+message+'</b><BR>');
		this.fireEvent('fileselectionerror', message);
	}
	,stdUploadSuccess:function(form, action){
		form.el.fileRec.set('progress',100);
		form.el.fileRec.set('status', 'Completo');
		form.el.fileRec.commit();
		this.processedFiles++;
		this.fireEvent('fileupload', this, true, action.result);

		if(this.addedFiles == this.processedFiles)
			this.fireEvent('allfilesupload');
	}
	,stdUploadFail:function(form, action){
		this.updateFile(form.el.fileRec, 'status', 'Erro');
		this.processedFiles++;
		this.fireEvent('fileupload', this, false, action.result);

		if(this.addedFiles == this.processedFiles)
			this.fireEvent('allfilesupload');

		this.fileAlert('<BR>'+form.el.fileRec.get('name')+'<BR><b>'+action.result.error+'</b><BR>');
	}
	,stdUploadFileSelected:function(fileBrowser, fileName){

		var lastSlash = fileName.lastIndexOf('/'); //check for *nix full file path
		if( lastSlash < 0 ){
			lastSlash = fileName.lastIndexOf('\\'); //check for win full file path
		}
		if(lastSlash > 0){
			fileName = fileName.substr(lastSlash+1);
		}
		var file = {
			name:fileName
			,size:'0'
		};

		if(Ext.isDefined(fileBrowser.fileInput.dom.files) ){
			file.size = fileBrowser.fileInput.dom.files[0].size;
		};

		var fileRec = this.addFile(file);

		if( file.size > this.maxFileSizeBytes){
			this.updateFile(fileRec, 'status', 'Erro');
			this.fileAlert('<BR>'+file.name+'<BR><b>File size exceeds allowed limit.</b><BR>');
			this.fireEvent('fileselectionerror', 'File size exceeds allowed limit.');
			return true;
		}

		var formEl = document.createElement('form'),
			extraPost;
		for( attr in this.extraPostData){
			extraPost = document.createElement('input'),
			extraPost.type = 'hidden';
			extraPost.name = attr;
			extraPost.value = this.extraPostData[attr];
			formEl.appendChild(extraPost);
		}
		formEl = this.el.appendChild(formEl);
		formEl.fileRec = fileRec;
		fileBrowser.fileInput.addClass('au-hidden');
		formEl.appendChild(fileBrowser.fileInput);
		formEl.addClass('au-hidden');
		var formSubmit = new Ext.form.BasicForm(formEl,{
			method:'POST'
			,fileUpload:true
		});

		formSubmit.submit({
			url:this.standardUploadUrl
			,scope:this
			,success:this.stdUploadSuccess
			,failure:this.stdUploadFail
		});
		this.updateFile(fileRec, 'status', 'Enviando');
		this.initStdUpload(); //re-init uploader for multiple simultaneous uploads
	}

        /**
	* Carrega um array de JSONS para o store do GRID
	* @param data {JSON[]}
	*/
	,loadRecords: function(data)
	{
            for(var i=0; i < data.length; i++)
            {
                this.addCompleteFile(data[i]);
            }
	}

        ,serializeFunction: function(){
            var response = []
            this.fileGrid.getStore().each(function(obj){
                    var o = {
                        id: obj.data.id
                        ,name: obj.data.name
                        ,path: obj.data.path
                    };
                    response.push(o);
            });
            return Ext.util.JSON.encode(response);
        }

});