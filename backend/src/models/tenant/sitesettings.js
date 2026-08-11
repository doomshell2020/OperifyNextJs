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
      allowNull: false
    },
    renew_days: {
      type: DataTypes.STRING(3),
      allowNull: false
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
      allowNull: true
    },
    print: {
      type: DataTypes.INTEGER,
      allowNull: false,
      defaultValue: 0
    },
    layout: {
      type: DataTypes.STRING(15),
      allowNull: false
    },
    general: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    receipt: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    id_card: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    school_code: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    affiliation_no: {
      type: DataTypes.STRING(15),
      allowNull: true
    },
    tc: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    employee_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    palace: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    site_url: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    branch_type: {
      type: DataTypes.STRING(45),
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
      {
        name: "id",
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
