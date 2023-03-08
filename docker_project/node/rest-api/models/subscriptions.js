const mongoose = require('mongoose');
const Schema = mongoose.Schema;

// Create subscriptions Schema and model
const Sub_Schema = new Schema({

    data:{ type: [{}] },
    subscriptionId:{
        type: String,
        required : true
    } 
});

const Subscription = mongoose.model('subscriptions', Sub_Schema);

// Export model Subscription
module.exports = Subscription;