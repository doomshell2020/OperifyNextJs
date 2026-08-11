const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('st_purchasereturn', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    retrundate: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    vendor_id: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    bill_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    bill_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    grn_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    purchaseorder_id: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    amount: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    }
  }, {
    sequelize,
    tableName: 'st_purchasereturn',
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
