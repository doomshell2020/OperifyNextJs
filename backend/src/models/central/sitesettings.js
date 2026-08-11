const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('sitesettings', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    first_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    last_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    mobile: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    contact_email: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    facebook_url: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    twitter_url: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    site_title: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    site_keywords: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    fine_rate: {
      type: DataTypes.STRING(200),
      allowNull: false,
      defaultValue: "null"
    },
    renew_days: {
      type: DataTypes.STRING(30),
      allowNull: false,
      defaultValue: "null"
    },
    site_description: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    google_analytics: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    modified: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: "0000-00-00 00:00:00"
    },
    print: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0
    },
    logo: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'sitesettings',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
