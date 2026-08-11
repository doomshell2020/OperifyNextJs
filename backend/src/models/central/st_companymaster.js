const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_companymaster', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    cname: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    vat_no: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    tin_no: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    tin_date: {
      type: DataTypes.DATEONLY,
      allowNull: false
    },
    address: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "Y"
    },
    date_added: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    accountno: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    gst: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    ifsc: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    main_branch: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "N"
    },
    pan_no: {
      type: DataTypes.STRING(45),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'st_companymaster',
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
