(function(){

	var getNode = function(s){
		return document.querySelector(s);
	},
	
	//get required node
	textarea = getNode('.chat textarea'),
	chatName = getNode('.chat-name'),
	status = getNode('.chat-status span'),
	messages = getNode('.chat-messages'),
	
	statusDefault = status.textContent,
	
	setStatus = function(s){
		status.textContent = s;
		
		if(s !== statusDefault){
			var delay = setTimeout(function(){
				setStatus(statusDefault);
				clearInterval(delay);
			}, 3000);
		}
	},
	
	notifyClient = function(name){
		if(Notification !== undefined){
			if(Notification.permission === "granted"){
				var opt = {
					body: "There's a new post from: " + name,
					tag: name
				};
				var notification = new Notification("New Post!", opt);
				notification.onclick = function(){
					window.focus();
					notification.close();
				};
			}
		}
	};
	
	
	
	try{
		var socket = io.connect('http://webdev.com:8080');
	}catch(e){
		setStatus('Cannot connect to socket.io');
	}
	
	if(socket !== undefined){
		//listen for output
		socket.on('output', function(data){
			if(data.length){
				//loop through results
				for(var x = 0; x < data.length; x = x + 1){
					var message = document.createElement('div'),
						sender = document.createElement('div'),
						context = document.createElement('div'),
						userIcon = document.createElement('span'),
						files = data[x].files,
						date = new Date(data[x].timestamp);
					
					userIcon.setAttribute('class', 'glyphicon glyphicon-user');
					
					sender.setAttribute('class', 'chat-message-sender panel-heading');
					sender.innerHTML = '<span class="glyphicon glyphicon-user"></span> ' + data[x].name +' <span class="pull-right">'+ date.toLocaleString() + '</span>';
					
					context.setAttribute('class', 'chat-message-context panel-body');
					context.innerHTML = data[x].message.replace(/\n/g, '<br />');
					
					if(files.length > 0){
						var newLine =  document.createElement('br'),
							imgRow = document.createElement('div'),
							imgColWidth = 'col-md-4';
							
						context.appendChild(newLine);
						imgRow.setAttribute('class','row');
						
						if(files.length < 3) imgColWidth = 'col-md-6';
						
						for(var img = 0; img < files.length; img = img + 1){
							var image = document.createElement('img'),
								imgCol = document.createElement('div'),
								imgLink = document.createElement('a');
								
								
							image.src = 'uploads/' + files[img]; // <img src="abcd.jpg">
							image.setAttribute('class','img-responsive');
							
							imgLink.href = 'uploads/' + files[img];
							imgLink.setAttribute('class','thumbnail');
							imgLink.setAttribute('data-toggle','lightbox');
							imgLink.setAttribute('data-gallery','imagesizes');
							imgLink.appendChild(image); //<a href="#" class="thumbnail"><img src="abcd.jpg"></a>
							
							imgCol.setAttribute('class', imgColWidth);
							imgCol.appendChild(imgLink); // <div class="col-md-4"><a href="#" class="thumbnail"><img src="abcd.jpg"></a></div>
							
							imgRow.appendChild(imgCol);
						}
						
						context.appendChild(imgRow);
					}
					
					message.setAttribute('class', 'chat-message panel panel-primary');					
					message.appendChild(sender);
					message.appendChild(context);

					//append
					messages.appendChild(message);
					messages.insertBefore(message, messages.firstChild);
					
				}
				
				if(data.length === 1){
					notifyClient(data[0].name);
				}
			}
		});
		
		//listen for a status
		socket.on('status', function(data){
			setStatus((typeof data === 'object') ? data.message : data);
			
			if(data.clear === true){
				textarea.value = '';
			}
		});
		
		//listen for keydown
		textarea.addEventListener('keydown', function(event){
			var self = this,
				name = chatName.value,
				date = new Date();
			
			
			if(event.which === 13 && event.shiftKey === false){
				event.preventDefault();
				
				socket.emit('input', {
					name: name,
					message: self.value,
					files: aUploadedFiles,
					timestamp: date.getTime()
				});
				
				if(Notification !== undefined){
					if(Notification.permission !== "granted") Notification.requestPermission();
				}else{
					console.log('browser notifications not supported.');
				}

				document.getElementById('reset').click();
			}
		});
	}
})();