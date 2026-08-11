const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('sitesettings_details', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    sitesettings_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    logo: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    sign: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    header_logo: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    address1: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    address2: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    phone: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    fax: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    email: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    website: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    subtitle1: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    subtitle2: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "Y"
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    affiliation_no: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    school_code: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    small_logo: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    icon: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    company_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    pan_number: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    gst_no: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    tin_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    account_number: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    ifsc: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    address: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    tc_logo: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    cmobile_no: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    ac_holder: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    bank_name: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    bank_branch_name: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    alias: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'sitesettings_details',
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
