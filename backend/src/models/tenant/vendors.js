const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vendors', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    name: {
      type: DataTypes.STRING(255),
      allowNull: false,
      unique: "name"
    },
    address: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    state_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    contact_no: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    email: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    vat_no: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    tin_no: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    tin_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    gst_number: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    pancard_number: {
      type: DataTypes.STRING(15),
      allowNull: true,
      comment: "\n"
    },
    tds: {
      type: DataTypes.ENUM('0','1'),
      allowNull: false,
      defaultValue: "0"
    },
    description: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false
    },
    contact_person: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    type: {
      type: DataTypes.ENUM('Vendor','Transporter','Customer'),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'vendors',
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
        name: "name",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "name" },
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
