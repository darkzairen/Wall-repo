var mongo = require('mongodb').MongoClient,
	client = require('socket.io').listen(8080).sockets;
	

mongo.connect('mongodb://webdev.com/wall', function(err, db){
	if(err) throw err;
	
	client.on('connection', function(socket){
		
		var col = db.collection('messages'),
			userCol = db.collection('users'),
			sendStatus = function(s){
				socket.emit('status', s)
			};
		
		socket.on('login', function(data){
			var userid = data.userid;
			
			userCol.find({userid:userid}).toArray(function(err, res){
				if(err) throw err;
				// console.log(res);
				client.emit('username', res);
			});
			
			
		});
		
		var options = {
			"sort": [['timestamp','asc']]
		}
		
		//emit all messages
		col.find({},options).toArray(function(err, res){
			if(err) throw err;
			// console.log(res);
			socket.emit('output', res);
		});
		// col.find().sort({"sort": timestamp}).limit(100).toArray(function(err, res){
			// if(err) throw err;
			// socket.emit('output', res);
		// });
		
		//wait for input
		socket.on('input', function(data){
			var name = data.name,
				message = data.message
				files = data.files
				whitespacePattern = /^\s*$/,
				date = new Date(),
				toBeInserted = {};
				
			var timestamp = date.getTime();
				
			// console.log(dateStamp);
			
			if(whitespacePattern.test(name) || whitespacePattern.test(message)){
				sendStatus('Name and message is required.');
			}else{
				toBeInserted = {
					name: name, 
					message: message, 
					files: files,
					timestamp : timestamp
				};
				console.log(toBeInserted);
				col.insert(toBeInserted, function(){
					
					//emit latest message to al clients
					client.emit('output', [data]);
					
					sendStatus({
						message: "Message sent",
						clear: true
					});
					
				});
			}
				
		});
		
	});
});	
