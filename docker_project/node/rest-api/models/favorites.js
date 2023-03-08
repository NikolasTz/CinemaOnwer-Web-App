const mongoose = require('mongoose');
const Schema = mongoose.Schema;

// Create favorites Schema and model
const Favorites_Schema = new Schema({

    USERID:{
        type: String,
        required : true
    },
    MOVEID:{
        type: String,
        required : true
    }
});

const Favorites = mongoose.model('favorites', Favorites_Schema);

// Export model Favorites
module.exports = Favorites;