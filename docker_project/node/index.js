const express = require('express');
const bodyParser = require('body-parser');
const mongoose = require('mongoose');

// Set up express app
const app = express();

// Use body-parser middleware
app.use(bodyParser.json());

// Connect to mongodb
mongoose.connect('mongodb://nikos:nikos@mongo:27017/cinema?authSource=admin',{ useNewUrlParser: true })
        .then(() => console.log('MongoDB Connected'))
        .catch(err => console.log(err));

// Initialize routes
app.use('/api', require('./rest-api/routes/api'));

// Listen for requests
app.listen(27018, function(){
    console.log('Now listening for requests');
});

// Error handling middleware
app.use(function(err, req, res, next){
    // See properties of message
    console.log(err); 
    res.status(422).send({error: err.message});
});
